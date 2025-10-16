# 📖 Panduan Instalasi - Sistem e-Procurement RSUD Ibnu Sina

<p align="center">
  <img src="public/images/logorsis.png" alt="RSUD Ibnu Sina" width="200"/>
</p>

<p align="center">
  <strong>Sistem e-Procurement</strong><br>
  Aplikasi Manajemen Permintaan Pengadaan Barang dan Jasa Rumah Sakit
</p>

---

## 📋 Daftar Isi

- [Kebutuhan Sistem](#-kebutuhan-sistem)
- [Instalasi Development](#-instalasi-development)
- [Instalasi Production](#-instalasi-production)
- [Konfigurasi Database](#-konfigurasi-database)
- [Troubleshooting](#-troubleshooting)

---

## 🖥️ Kebutuhan Sistem

### Minimum Requirements

| Software | Versi Minimum | Versi Recommended |
|----------|--------------|-------------------|
| PHP | 8.2 | 8.3+ |
| Composer | 2.0 | Latest |
| Node.js | 18.x | 20.x LTS |
| NPM | 9.x | 10.x |
| Database | SQLite / MySQL 8.0 / PostgreSQL 13 | MySQL 8.0+ |

### Extension PHP yang Diperlukan

```
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- SQLite (jika menggunakan SQLite)
```

### Cek Versi Software

```bash
# Cek versi PHP
php -v

# Cek versi Composer
composer -V

# Cek versi Node.js
node -v

# Cek versi NPM
npm -v
```

---

## 🚀 Instalasi Development

### 1. Clone atau Download Repository

**Opsi A: Via Git**
```bash
git clone https://github.com/your-repo/pengadaan-app.git
cd pengadaan-app
```

**Opsi B: Manual Download**
- Download file ZIP dari repository
- Extract ke folder yang diinginkan
- Buka terminal/command prompt di folder tersebut

### 2. Install Dependencies PHP

```bash
composer install
```

Jika ada error terkait memory limit:
```bash
COMPOSER_MEMORY_LIMIT=-1 composer install
```

### 3. Install Dependencies JavaScript

```bash
npm install
```

atau menggunakan Yarn:
```bash
yarn install
```

### 4. Setup Environment File

**Windows:**
```bash
copy .env.example .env
```

**Linux/Mac:**
```bash
cp .env.example .env
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Konfigurasi Database

Edit file `.env` dan sesuaikan dengan database Anda:

**Untuk SQLite (Recommended untuk Development):**
```env
APP_NAME="e-Procurement RSUD"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# Pastikan file database.sqlite ada di folder database/
```

**Untuk MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pengadaan_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 7. Buat Database File (Untuk SQLite)

**Windows:**
```bash
type nul > database\database.sqlite
```

**Linux/Mac:**
```bash
touch database/database.sqlite
```

### 8. Jalankan Migration

```bash
php artisan migrate
```

Jika diminta konfirmasi, ketik `yes` dan tekan Enter.

### 9. Seed Data (Optional)

**Seed data user default:**
```bash
php artisan db:seed
```

**Seed data contoh IGD:**
```bash
php artisan db:seed --class=IGDPermintaanSeeder
```

### 10. Build Asset Frontend

**Development mode (dengan watch):**
```bash
npm run dev
```

**Production mode:**
```bash
npm run build
```

### 11. Jalankan Aplikasi

Buka terminal baru dan jalankan:

```bash
php artisan serve
```

Aplikasi akan berjalan di: **http://localhost:8000**

### 12. Setup Storage Link (Untuk Upload File)

```bash
php artisan storage:link
```

---

## 🏭 Instalasi Production

### 1. Persiapan Server

Pastikan server memenuhi requirements dan sudah terinstall:
- Web Server (Apache/Nginx)
- PHP 8.2+
- Database (MySQL/PostgreSQL)
- Composer
- Node.js & NPM

### 2. Clone Repository ke Server

```bash
cd /var/www/
git clone https://github.com/your-repo/pengadaan-app.git
cd pengadaan-app
```

### 3. Set Permission yang Tepat

```bash
# Set owner
sudo chown -R www-data:www-data /var/www/pengadaan-app

# Set permission untuk folder
sudo find /var/www/pengadaan-app -type d -exec chmod 755 {} \;

# Set permission untuk file
sudo find /var/www/pengadaan-app -type f -exec chmod 644 {} \;

# Set permission khusus untuk storage dan bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 4. Install Dependencies

```bash
# Install PHP dependencies (production only)
composer install --optimize-autoloader --no-dev

# Install NPM dependencies
npm ci --production
```

### 5. Setup Environment Production

```bash
cp .env.example .env
nano .env  # atau vim .env
```

Konfigurasi production:
```env
APP_NAME="e-Procurement RSUD Ibnu Sina"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://eprocurement.rsudibsnugresik.id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=pengadaan_production
DB_USERNAME=pengadaan_user
DB_PASSWORD=strong_password_here

# Cache & Session
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=noreply@rsudibsnugresik.id
MAIL_FROM_NAME="${APP_NAME}"
```

### 6. Generate Key & Setup Database

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
```

### 7. Build Asset untuk Production

```bash
npm run build
```

### 8. Optimize Application

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### 9. Setup Web Server

**Untuk Apache** (`/etc/apache2/sites-available/pengadaan.conf`):
```apache
<VirtualHost *:80>
    ServerName eprocurement.rsudibsnugresik.id
    ServerAlias www.eprocurement.rsudibsnugresik.id
    DocumentRoot /var/www/pengadaan-app/public

    <Directory /var/www/pengadaan-app/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/pengadaan-error.log
    CustomLog ${APACHE_LOG_DIR}/pengadaan-access.log combined
</VirtualHost>
```

Aktifkan site:
```bash
sudo a2ensite pengadaan.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

**Untuk Nginx** (`/etc/nginx/sites-available/pengadaan`):
```nginx
server {
    listen 80;
    server_name eprocurement.rsudibsnugresik.id www.eprocurement.rsudibsnugresik.id;
    root /var/www/pengadaan-app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

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
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan site:
```bash
sudo ln -s /etc/nginx/sites-available/pengadaan /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 10. Setup SSL Certificate (Recommended)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx  # untuk Nginx
# atau
sudo apt install certbot python3-certbot-apache  # untuk Apache

# Generate SSL Certificate
sudo certbot --nginx -d eprocurement.rsudibsnugresik.id -d www.eprocurement.rsudibsnugresik.id
# atau untuk Apache
sudo certbot --apache -d eprocurement.rsudibsnugresik.id -d www.eprocurement.rsudibsnugresik.id
```

### 11. Setup Cron Job untuk Laravel Scheduler

```bash
crontab -e
```

Tambahkan baris berikut:
```
* * * * * cd /var/www/pengadaan-app && php artisan schedule:run >> /dev/null 2>&1
```

### 12. Setup Queue Worker (Optional)

Jika menggunakan queue untuk background jobs:

**Menggunakan Supervisor:**
```bash
sudo apt install supervisor
```

Buat file konfigurasi `/etc/supervisor/conf.d/pengadaan-worker.conf`:
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

Reload supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start pengadaan-worker:*
```

---

## 🗄️ Konfigurasi Database

### SQLite (Development)

1. Buat file database:
   ```bash
   touch database/database.sqlite
   ```

2. Update `.env`:
   ```env
   DB_CONNECTION=sqlite
   ```

3. Jalankan migration:
   ```bash
   php artisan migrate
   ```

### MySQL

1. Buat database:
   ```sql
   CREATE DATABASE pengadaan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'pengadaan_user'@'localhost' IDENTIFIED BY 'strong_password';
   GRANT ALL PRIVILEGES ON pengadaan_db.* TO 'pengadaan_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

2. Update `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=pengadaan_db
   DB_USERNAME=pengadaan_user
   DB_PASSWORD=strong_password
   ```

3. Jalankan migration:
   ```bash
   php artisan migrate
   ```

### PostgreSQL

1. Buat database:
   ```bash
   sudo -u postgres psql
   ```
   ```sql
   CREATE DATABASE pengadaan_db;
   CREATE USER pengadaan_user WITH ENCRYPTED PASSWORD 'strong_password';
   GRANT ALL PRIVILEGES ON DATABASE pengadaan_db TO pengadaan_user;
   ```

2. Update `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=pengadaan_db
   DB_USERNAME=pengadaan_user
   DB_PASSWORD=strong_password
   ```

3. Jalankan migration:
   ```bash
   php artisan migrate
   ```

---

## 🔧 Troubleshooting

### Error: "No application encryption key has been specified"

**Solusi:**
```bash
php artisan key:generate
```

### Error: "Class 'ZipArchive' not found"

**Solusi (Ubuntu/Debian):**
```bash
sudo apt-get install php-zip
sudo systemctl restart apache2  # atau nginx
```

**Solusi (Windows dengan XAMPP):**
- Buka `php.ini`
- Uncomment: `;extension=zip` → `extension=zip`
- Restart Apache

### Error: Permission denied (Storage/Cache)

**Solusi (Linux/Mac):**
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

**Solusi (Windows):**
- Klik kanan folder `storage` dan `bootstrap/cache`
- Properties → Security → Edit
- Berikan Full Control untuk user Anda

### Error: "SQLSTATE[HY000] [2002] Connection refused"

**Solusi:**
1. Pastikan MySQL/PostgreSQL service berjalan:
   ```bash
   sudo systemctl status mysql  # atau postgresql
   sudo systemctl start mysql   # jika belum running
   ```

2. Cek konfigurasi `.env`:
   - `DB_HOST` benar (biasanya `127.0.0.1` atau `localhost`)
   - `DB_PORT` benar (MySQL: 3306, PostgreSQL: 5432)
   - Username dan password benar

### Error: npm install gagal

**Solusi:**
```bash
# Hapus node_modules dan package-lock.json
rm -rf node_modules package-lock.json

# Install ulang
npm cache clean --force
npm install
```

### Error: Vite manifest not found

**Solusi:**
```bash
# Build ulang asset
npm run build
```

### Error: 500 Internal Server Error

**Solusi:**
1. Cek log error:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. Aktifkan debug mode di `.env`:
   ```env
   APP_DEBUG=true
   ```

3. Clear cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

### Error: Mix manifest tidak ditemukan

**Solusi:**
```bash
npm run build
```

### Database migration error

**Solusi:**
```bash
# Rollback dan migrate ulang
php artisan migrate:fresh

# Jika ingin dengan seed data
php artisan migrate:fresh --seed
```

---

## 📞 Support & Bantuan

Jika mengalami masalah yang tidak tercantum di dokumentasi:

1. **Cek Log Error:**
   - Laravel: `storage/logs/laravel.log`
   - Web Server: Apache/Nginx error logs
   - PHP: PHP error logs

2. **Clear Cache:**
   ```bash
   php artisan optimize:clear
   ```

3. **Dokumentasi Resmi:**
   - Laravel: https://laravel.com/docs
   - Inertia.js: https://inertiajs.com
   - Vue.js: https://vuejs.org

4. **Kontak Developer:**
   - Email: it@rsudibsnugresik.id
   - Tim IT RSUD Ibnu Sina Kabupaten Gresik

---

## 📝 Catatan Penting

- ⚠️ **Jangan** commit file `.env` ke repository
- 🔒 **Selalu** gunakan HTTPS di production
- 🔑 **Gunakan** password yang kuat untuk database
- 💾 **Backup** database secara berkala
- 🔄 **Update** dependencies secara rutin untuk keamanan

---

**Versi Dokumentasi:** 1.0  
**Terakhir Diperbarui:** Oktober 2025  
**Untuk:** Sistem e-Procurement RSUD Ibnu Sina Kabupaten Gresik
