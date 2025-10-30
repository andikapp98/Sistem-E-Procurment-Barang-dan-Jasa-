# Quick Start Guide - Development Server

## ⚡ TL;DR - Jalankan Dev Server

```bash
# Gunakan YARN (bukan NPM)
yarn dev
```

Akses: **http://localhost:5173**

---

## 🔧 Setup Pertama Kali

```bash
# 1. Install Yarn (sekali saja)
npm install -g yarn

# 2. Install dependencies
yarn install

# 3. Jalankan dev server
yarn dev
```

---

## 📋 Common Commands

```bash
# Development
yarn dev                    # Start dev server

# Building
yarn build                  # Build production

# Package Management
yarn add <package>          # Install package
yarn add -D <package>       # Install dev package
yarn remove <package>       # Uninstall package

# Maintenance
yarn cache clean            # Clear cache
rm -rf node_modules         # Remove all packages
yarn install                # Fresh install
```

---

## 🚨 Troubleshooting

### "vite is not recognized"
**Solution:** Gunakan `yarn dev` bukan `npm run dev`

### "EPERM: operation not permitted"
**Solution:**
1. Stop all dev servers
2. Close VSCode
3. Restart terminal
4. Run `yarn install` again

### "Cannot find module"
```bash
yarn cache clean
rm -rf node_modules
yarn install
```

---

## ✅ Verification

Dev server sukses jika muncul:
```
VITE v7.1.11  ready in 930 ms
➜  Local:   http://localhost:5173/
LARAVEL v12.33.0  plugin v2.0.1
```

---

## 📦 Required Packages (All Installed)

- ✅ vite@7.1.11
- ✅ laravel-vite-plugin@2.0.1
- ✅ @vitejs/plugin-vue@6.0.1
- ✅ @inertiajs/vue3
- ✅ @headlessui/vue
- ✅ Vue 3, Tailwind CSS, Axios

---

## 🎯 Development Workflow

```bash
# Terminal 1 - Frontend (Vite)
yarn dev

# Terminal 2 - Backend (Laravel)
php artisan serve
```

**Access:**
- Frontend HMR: http://localhost:5173
- Laravel API: http://localhost:8000

---

## ⚠️ Important Notes

1. **Always use YARN** for this project (not npm)
2. **Don't delete** yarn.lock
3. **Commit** yarn.lock to git
4. **Don't mix** npm and yarn commands

---

## 📝 Package Manager Choice

**Why YARN over NPM for this project:**
- ✅ More reliable on Windows
- ✅ Better dependency resolution
- ✅ Faster installs
- ✅ Deterministic builds
- ✅ Better lockfile

---

## 🎉 Ready to Code!

Server is running, go to:
👉 **http://localhost:5173**

Happy coding! 🚀
