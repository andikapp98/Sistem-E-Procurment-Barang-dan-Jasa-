# ✅ Deployment Checklist - Sistem e-Procurement

Checklist lengkap untuk deployment ke production server.

---

## 📋 Pre-Deployment Checklist

### 1. Code & Dependencies
- [ ] Semua perubahan sudah di-commit ke Git
- [ ] Branch production sudah up-to-date
- [ ] Dependencies sudah diupdate (`composer update`, `npm update`)
- [ ] Code sudah melewati code review
- [ ] Tests sudah passing semua (`php artisan test`)
- [ ] Code style sudah sesuai standar (`./vendor/bin/pint --test`)

### 2. Environment Configuration
- [ ] File `.env.example` sudah diupdate dengan variabel terbaru
- [ ] Tidak ada credentials di code (gunakan environment variables)
- [ ] `APP_ENV` di set ke `production`
- [ ] `APP_DEBUG` di set ke `false`
- [ ] `APP_URL` sudah sesuai dengan domain production
- [ ] Database credentials sudah benar
- [ ] Mail configuration sudah disetup
- [ ] Cache driver sudah optimal (redis/memcached)
- [ ] Session driver sudah sesuai
- [ ] Queue connection sudah disetup

### 3. Security
- [ ] Password default sudah diganti
- [ ] SSL Certificate sudah terinstall
- [ ] HTTPS redirect sudah aktif
- [ ] CSRF protection aktif
- [ ] XSS protection aktif
- [ ] File upload validation sudah proper
- [ ] Rate limiting sudah dikonfigurasi
- [ ] Sensitive routes sudah dilindungi middleware
- [ ] `.env` file tidak ter-commit ke Git
- [ ] Database backup sudah dijadwalkan

### 4. Database
- [ ] Migration files sudah lengkap
- [ ] Seeder untuk data master sudah siap
- [ ] Database sudah dibuat di server production
- [ ] Database user & privileges sudah disetup
- [ ] Database connection sudah ditest
- [ ] Index database sudah optimal
- [ ] Backup strategy sudah disiapkan

### 5. Assets & Files
- [ ] Frontend assets sudah di-build (`npm run build`)
- [ ] Images optimized (compressed)
- [ ] Unused assets sudah dihapus
- [ ] Storage link sudah dibuat (`php artisan storage:link`)
- [ ] File permissions sudah benar (775 untuk storage)
- [ ] .gitignore sudah proper

---

## 🚀 Deployment Steps

### Step 1: Server Preparation

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-redis \
mysql-server redis-server git composer nodejs npm

# Enable services
sudo systemctl enable nginx
sudo systemctl enable php8.2-fpm
sudo systemctl enable mysql
sudo systemctl enable redis-server
```

**Checklist:**
- [ ] PHP 8.2+ installed
- [ ] Web server (Nginx/Apache) installed
- [ ] Database server installed
- [ ] Redis installed
- [ ] Composer installed
- [ ] Node.js & NPM installed
- [ ] Git installed
- [ ] SSL certificate ready

### Step 2: Clone Repository

```bash
# Navigate to web root
cd /var/www

# Clone repository
sudo git clone https://github.com/your-repo/pengadaan-app.git
cd pengadaan-app

# Checkout to production branch
sudo git checkout main
```

**Checklist:**
- [ ] Repository cloned successfully
- [ ] Correct branch checked out
- [ ] All files present

### Step 3: Install Dependencies

```bash
# Install PHP dependencies (production only, optimized)
sudo composer install --optimize-autoloader --no-dev

# Install NPM dependencies
sudo npm ci --production
```

**Checklist:**
- [ ] Composer dependencies installed
- [ ] NPM dependencies installed
- [ ] No errors during installation

### Step 4: Environment Setup

```bash
# Copy environment file
sudo cp .env.example .env

# Edit environment file
sudo nano .env
```

**Update .env with production values:**
```env
APP_NAME="e-Procurement RSUD Ibnu Sina"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://eprocurement.rsudibsnugresik.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pengadaan_production
DB_USERNAME=pengadaan_user
DB_PASSWORD=YOUR_STRONG_PASSWORD

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=noreply@rsudibsnugresik.id
MAIL_FROM_NAME="${APP_NAME}"
```

```bash
# Generate application key
sudo php artisan key:generate
```

**Checklist:**
- [ ] .env file created
- [ ] All variables configured correctly
- [ ] APP_KEY generated
- [ ] Database credentials correct
- [ ] Mail configuration correct

### Step 5: Database Setup

```bash
# Login to MySQL
sudo mysql -u root -p

# Create database and user
CREATE DATABASE pengadaan_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'pengadaan_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON pengadaan_production.* TO 'pengadaan_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
sudo php artisan migrate --force

# Seed data (if needed)
sudo php artisan db:seed --force
```

**Checklist:**
- [ ] Database created
- [ ] Database user created with proper privileges
- [ ] Migrations ran successfully
- [ ] Seed data loaded (if needed)
- [ ] Test database connection

### Step 6: Build Assets

```bash
# Build frontend assets for production
sudo npm run build
```

**Checklist:**
- [ ] Assets built successfully
- [ ] No build errors
- [ ] Build files exist in public/build/

### Step 7: Set Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/pengadaan-app

# Set directory permissions
sudo find /var/www/pengadaan-app -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/pengadaan-app -type f -exec chmod 644 {} \;

# Special permissions for storage and cache
sudo chmod -R 775 storage bootstrap/cache

# Create storage link
sudo php artisan storage:link
```

**Checklist:**
- [ ] Ownership set to www-data
- [ ] Directory permissions: 755
- [ ] File permissions: 644
- [ ] Storage permissions: 775
- [ ] Cache permissions: 775
- [ ] Storage link created

### Step 8: Optimize Application

```bash
# Cache configuration
sudo php artisan config:cache

# Cache routes
sudo php artisan route:cache

# Cache views
sudo php artisan view:cache

# Optimize autoloader
sudo composer dump-autoload --optimize
```

**Checklist:**
- [ ] Config cached
- [ ] Routes cached
- [ ] Views cached
- [ ] Autoloader optimized

### Step 9: Configure Web Server

#### For Nginx

Create `/etc/nginx/sites-available/pengadaan`:

```nginx
server {
    listen 80;
    server_name eprocurement.rsudibsnugresik.id www.eprocurement.rsudibsnugresik.id;
    root /var/www/pengadaan-app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/javascript application/json;
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/pengadaan /etc/nginx/sites-enabled/

# Test Nginx configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

**Checklist:**
- [ ] Nginx config file created
- [ ] Site enabled
- [ ] Nginx config test passed
- [ ] Nginx restarted successfully
- [ ] Site accessible via HTTP

### Step 10: Setup SSL Certificate

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Generate SSL certificate
sudo certbot --nginx -d eprocurement.rsudibsnugresik.id -d www.eprocurement.rsudibsnugresik.id

# Test auto-renewal
sudo certbot renew --dry-run
```

**Checklist:**
- [ ] Certbot installed
- [ ] SSL certificate generated
- [ ] HTTPS working
- [ ] HTTP redirects to HTTPS
- [ ] Auto-renewal setup

### Step 11: Setup Cron & Queue Worker

```bash
# Setup cron for Laravel scheduler
crontab -e

# Add this line:
* * * * * cd /var/www/pengadaan-app && php artisan schedule:run >> /dev/null 2>&1

# Setup Supervisor for queue worker
sudo apt install supervisor

# Create supervisor config
sudo nano /etc/supervisor/conf.d/pengadaan-worker.conf
```

Content for supervisor config:
```ini
[program:pengadaan-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pengadaan-app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/pengadaan-app/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start pengadaan-worker:*

# Check status
sudo supervisorctl status
```

**Checklist:**
- [ ] Cron job added
- [ ] Supervisor installed
- [ ] Worker config created
- [ ] Workers running
- [ ] Worker logs accessible

---

## 🧪 Post-Deployment Testing

### 1. Basic Functionality
- [ ] Homepage loads correctly
- [ ] Static assets (CSS, JS, images) loading
- [ ] Login page accessible
- [ ] Can login with credentials
- [ ] Dashboard displays correctly
- [ ] Navigation working

### 2. Core Features
- [ ] Create permintaan works
- [ ] View permintaan list works
- [ ] Edit permintaan works
- [ ] Delete permintaan works
- [ ] Search/filter works
- [ ] Profile update works
- [ ] Password change works
- [ ] Logout works

### 3. Performance
- [ ] Page load time < 3 seconds
- [ ] Database queries optimized
- [ ] No N+1 query problems
- [ ] Caching working properly
- [ ] Assets compressed (Gzip)

### 4. Security
- [ ] HTTPS enabled and working
- [ ] HTTP redirects to HTTPS
- [ ] CSRF protection working
- [ ] XSS protection working
- [ ] SQL injection protection working
- [ ] No sensitive data exposed
- [ ] Error pages don't show stack traces

### 5. Email & Notifications
- [ ] Password reset email works
- [ ] Email configuration correct
- [ ] Mail queue processing

---

## 📊 Monitoring Setup

### Application Monitoring
```bash
# Check application logs
tail -f storage/logs/laravel.log

# Check Nginx logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log

# Check PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log
```

**Checklist:**
- [ ] Laravel logs accessible
- [ ] Nginx logs accessible
- [ ] PHP logs accessible
- [ ] Log rotation configured
- [ ] Disk space monitoring setup

### Database Monitoring
```bash
# Check MySQL status
sudo systemctl status mysql

# Check database size
mysql -u root -p -e "SELECT table_schema AS 'Database', ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)' FROM information_schema.TABLES GROUP BY table_schema;"
```

**Checklist:**
- [ ] Database service running
- [ ] Database size monitored
- [ ] Slow query log enabled
- [ ] Database backup automated

---

## 💾 Backup Strategy

### Database Backup
```bash
# Create backup script
sudo nano /usr/local/bin/backup-pengadaan-db.sh
```

Content:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/pengadaan"
DB_NAME="pengadaan_production"
DB_USER="pengadaan_user"
DB_PASS="strong_password"

mkdir -p $BACKUP_DIR
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/pengadaan_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "pengadaan_*.sql.gz" -mtime +30 -delete
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/backup-pengadaan-db.sh

# Add to crontab (daily at 2 AM)
0 2 * * * /usr/local/bin/backup-pengadaan-db.sh
```

**Checklist:**
- [ ] Backup script created
- [ ] Backup cron job added
- [ ] Backup directory exists
- [ ] Backups tested (restoration)
- [ ] Old backups cleanup automated

---

## 🔄 Update & Maintenance

### Deployment Update Checklist
```bash
# Pull latest code
cd /var/www/pengadaan-app
sudo git pull origin main

# Update dependencies
sudo composer install --optimize-autoloader --no-dev
sudo npm ci --production

# Build assets
sudo npm run build

# Run migrations
sudo php artisan migrate --force

# Clear and recache
sudo php artisan optimize:clear
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
sudo supervisorctl restart pengadaan-worker:*
```

**Checklist:**
- [ ] Code updated
- [ ] Dependencies updated
- [ ] Assets rebuilt
- [ ] Migrations ran
- [ ] Caches cleared
- [ ] Services restarted
- [ ] No errors in logs

---

## 📞 Emergency Contacts

**Technical Team:**
- Lead Developer: [Name] - [Phone] - [Email]
- System Admin: [Name] - [Phone] - [Email]
- Database Admin: [Name] - [Phone] - [Email]

**Escalation:**
- IT Manager: [Name] - [Phone] - [Email]
- CTO/IT Director: [Name] - [Phone] - [Email]

---

## 📝 Deployment Log

**Deployment Date:** _______________  
**Deployed By:** _______________  
**Version/Tag:** _______________  
**Database Backup:** _______________  
**Rollback Plan:** _______________  

**Issues Encountered:**
- 
- 

**Resolution:**
- 
- 

**Sign-off:**
- Developer: _______________ Date: _______________
- Tester: _______________ Date: _______________
- System Admin: _______________ Date: _______________
- Project Manager: _______________ Date: _______________

---

**Checklist Version:** 1.0  
**Last Updated:** 16 Oktober 2025
