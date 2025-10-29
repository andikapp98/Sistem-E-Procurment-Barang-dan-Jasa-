/**
 * CSRF Token Management Utility
 * 
 * Prevents 419 Page Expired errors by refreshing CSRF tokens
 * before form submissions
 */

export const csrfUtils = {
    /**
     * Refresh CSRF token from server
     */
    async refreshToken() {
        try {
            const response = await fetch('/sanctum/csrf-cookie', {
                credentials: 'same-origin',
            });
            
            if (response.ok) {
                // Get new token from meta tag
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                // Update all CSRF token inputs
                document.querySelectorAll('input[name="_token"]').forEach(input => {
                    if (token) {
                        input.value = token;
                    }
                });
                
                return token;
            }
        } catch (error) {
            console.error('Failed to refresh CSRF token:', error);
        }
        return null;
    },

    /**
     * Add CSRF token to form data
     */
    addToFormData(formData) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token && formData instanceof FormData) {
            formData.append('_token', token);
        }
        return formData;
    },

    /**
     * Get current CSRF token
     */
    getToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    },

    /**
     * Setup auto-refresh on visibility change
     */
    setupAutoRefresh() {
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.refreshToken();
            }
        });
    },

    /**
     * Setup refresh before form submissions
     */
    setupFormRefresh() {
        // Intercept all form submissions
        document.addEventListener('submit', async (e) => {
            const form = e.target;
            
            // Check if this is a POST/PUT/DELETE/PATCH request
            const method = (form.querySelector('input[name="_method"]')?.value || form.method).toUpperCase();
            
            if (['POST', 'PUT', 'DELETE', 'PATCH'].includes(method)) {
                // Check if form has CSRF token
                const tokenInput = form.querySelector('input[name="_token"]');
                
                if (tokenInput) {
                    // Refresh token before submission
                    const newToken = await this.refreshToken();
                    if (newToken) {
                        tokenInput.value = newToken;
                    }
                }
            }
        }, { capture: true });
    }
};

// Auto-initialize on load
if (typeof window !== 'undefined') {
    window.csrfUtils = csrfUtils;
    
    // Setup auto-refresh mechanisms
    document.addEventListener('DOMContentLoaded', () => {
        csrfUtils.setupAutoRefresh();
        // Don't auto-setup form refresh as it might interfere with Inertia
        // csrfUtils.setupFormRefresh();
    });
}

export default csrfUtils;
