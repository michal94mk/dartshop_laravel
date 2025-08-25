# 🚀 DartShop - Production Deployment Guide

## ⚡ Quick Start (3 Commands)

### **Complete Deployment in 3 Steps:**

#### **Step 1: Server Setup (one command)**
```bash
curl -fsSL https://raw.githubusercontent.com/michal94mk/dartshop_laravel/master/deployment/server-setup.sh | bash
```
*This installs: PHP 8.2, Nginx, MySQL, Node.js, Composer, Supervisor and configures basic settings*

#### **Step 2: Database Configuration**
```bash
sudo mysql -u root -p
```
```sql
CREATE DATABASE dartshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dartshop'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON dartshop.* TO 'dartshop'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### **Step 3: Application Deployment (one command)**
```bash
cd /var/www/dartshop_laravel && git clone https://github.com/michal94mk/dartshop_laravel.git . && chmod +x deployment/deploy.sh && DOMAIN=your-domain.com ./deployment/deploy.sh --first-time
```

**🎉 Done! Your application will be running at `http://your-domain.com`**

### **What happens during deployment:**
- ✅ Automatic dependency installation
- ✅ Environment file creation (`.env`)
- ✅ Database connection testing
- ✅ Migrations and seeding
- ✅ Frontend asset building
- ✅ Permission configuration
- ✅ Post-deployment instructions

### **⚠️ Important Notes:**
- Replace `your-domain.com` with your actual domain
- Replace `your_strong_password` with a secure password
- The deployment script will ask you to edit `.env` file during setup
- You'll need to configure Nginx and SSL separately (see detailed guide below)
- For production use, configure email settings, Stripe keys, and Google OAuth

### **🚫 Common Mistakes:**
- **DON'T run Step 3 before Step 1** - you'll get "command not found" errors
- **DON'T skip database setup** - the app won't work without it
- **DON'T use weak passwords** - use strong, unique passwords
- **DON'T forget to configure your domain DNS** to point to the server

### **🔒 Alternative Method (More Secure):**
If you prefer not to run scripts directly from the internet:

```bash
# 1. Clone repository first
git clone https://github.com/michal94mk/dartshop_laravel.git
cd dartshop_laravel

# 2. Review scripts before running
cat deployment/server-setup.sh
cat deployment/deploy.sh

# 3. Run server setup locally
chmod +x deployment/server-setup.sh
./deployment/server-setup.sh

# 4. Configure database (same as above)
sudo mysql -u root -p
# ... database commands ...

# 5. Run deployment
sudo mkdir -p /var/www/dartshop_laravel
sudo cp -r . /var/www/dartshop_laravel/
cd /var/www/dartshop_laravel
sudo chown -R $USER:www-data .
DOMAIN=your-domain.com ./deployment/deploy.sh --first-time
```

---

## 📋 System Requirements

### Server
- **OS**: Ubuntu 20.04+ / Debian 11+ / CentOS 8+
- **RAM**: Minimum 2GB (4GB+ recommended)
- **Disk**: Minimum 20GB SSD
- **CPU**: 2 cores (4+ recommended)

### Software
- **PHP**: 8.2+
- **Node.js**: 18+ (20 LTS recommended)
- **Nginx**: 1.18+
- **MySQL**: 8.0+ or PostgreSQL 13+
- **Redis**: Not currently used (may be added in future)
- **Supervisor**: for queue worker

## 🛠️ Deployment Steps

### 1. Prepare the Server

#### Automated Server Setup (Recommended):
```bash
# Download and run server setup script
curl -fsSL https://github.com/michal94mk/dartshop_laravel/master/deployment/server-setup.sh | bash

# Or if you have the repository cloned locally:
cd /path/to/dartshop_laravel
chmod +x deployment/server-setup.sh
./deployment/server-setup.sh
```

#### Manual Server Setup:
```bash
# Update the system
sudo apt update && sudo apt upgrade -y

# Install basic packages
sudo apt install -y curl wget git unzip software-properties-common

# Install PHP 8.2
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-redis \
    php8.2-xml php8.2-curl php8.2-mbstring php8.2-zip php8.2-gd \
    php8.2-intl php8.2-bcmath php8.2-soap php8.2-imagick

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install Nginx
sudo apt install -y nginx

# Install MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Redis is not currently used in this project
# sudo apt install -y redis-server

# Install Supervisor
sudo apt install -y supervisor
```

### 2. Database Setup

```sql
-- Create database and user
CREATE DATABASE dartshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dartshop'@'localhost' IDENTIFIED BY 'strong_db_password';
GRANT ALL PRIVILEGES ON dartshop.* TO 'dartshop'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Deploy the Application

#### One-Command Deployment:
```bash
# Clone and deploy in one command
cd /var/www/dartshop_laravel
git clone https://github.com/michal94mk/dartshop_laravel.git .
chmod +x deployment/deploy.sh
DOMAIN=your-domain.com ./deployment/deploy.sh --first-time
```

#### Manual Clone and Configure:
```bash
# Clone the repository
sudo mkdir -p /var/www/dartshop_laravel
cd /var/www/dartshop_laravel
sudo git clone https://github.com/michal94mk/dartshop_laravel.git .

# Set ownership
sudo chown -R $USER:www-data /var/www/dartshop_laravel

# Copy environment file
cp .env.example .env

# Edit configuration
nano .env
```

### 4. .env Configuration

```bash
APP_NAME="DartShop"
APP_ENV=production
APP_DEBUG=false
APP_KEY=
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dartshop
DB_USERNAME=dartshop
DB_PASSWORD=strong_db_password

CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file

# Redis configuration (not currently used)
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=
# REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.your-domain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@your-domain.com
MAIL_PASSWORD=mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="DartShop"

STRIPE_PUBLIC_KEY=pk_live_xxx
STRIPE_SECRET_KEY=sk_live_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

GOOGLE_CLIENT_ID=xxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=xxx

NOCAPTCHA_SECRET=xxx
NOCAPTCHA_SITEKEY=xxx
```

### 5. Installation and Setup

#### Automated Deployment (Recommended):
```bash
# Configure deployment (optional)
cp deployment/config.sh deployment/config.local.sh
nano deployment/config.local.sh  # Edit your settings
source deployment/config.local.sh

# Run first-time deployment
chmod +x deployment/deploy.sh
./deployment/deploy.sh --first-time
```

#### Manual Installation:
```bash
composer install --optimize-autoloader --no-dev
npm ci
npm run build
php artisan key:generate
php artisan storage:link
php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan app:setup-production
```

### 6. Nginx Configuration

```bash
# Copy configuration
sudo cp deployment/nginx.conf /etc/nginx/sites-available/dartshop
sudo ln -s /etc/nginx/sites-available/dartshop /etc/nginx/sites-enabled/

# Remove default site
sudo rm /etc/nginx/sites-enabled/default

# Test configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

### 7. SSL Certificate

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Test auto-renewal
sudo certbot renew --dry-run
```

### 8. Queue Worker Configuration

```bash
# Copy supervisor configuration
sudo cp deployment/supervisor.conf /etc/supervisor/conf.d/dartshop.conf

# Start supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start dartshop-worker:*
```

### 9. Cron Configuration

```bash
# Add cron jobs
sudo crontab -e
# Paste contents from deployment/crontab
```

### 10. Firewall Configuration

```bash
# UFW setup
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

## 🔒 Security

### PHP Security
```bash
# Edit php.ini
sudo nano /etc/php/8.2/fpm/php.ini

# Set values:
expose_php = Off
display_errors = Off
log_errors = On
allow_url_fopen = Off
allow_url_include = Off
```

### Nginx Security
```bash
# Hide Nginx version
echo "server_tokens off;" | sudo tee -a /etc/nginx/nginx.conf
```

## 📊 Monitoring and Logs

### Application Logs
```bash
# Laravel logs
tail -f /var/www/dartshop/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/dartshop_error.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

### Monitoring
- **Uptime**: Pingdom, UptimeRobot
- **Errors**: Sentry, Bugsnag
- **Performance**: New Relic, Datadog
- **Server**: Prometheus + Grafana

## 🔄 Updates & Deployment Options

### Standard Deployment:
```bash
./deployment/deploy.sh
```

### Advanced Deployment Options:
```bash
# Skip backup (faster)
./deployment/deploy.sh --no-backup

# Skip building assets (if no frontend changes)
./deployment/deploy.sh --skip-build

# Quick deployment (no backup, no build)
./deployment/deploy.sh --no-backup --skip-build

# Use custom domain
DOMAIN=your-domain.com ./deployment/deploy.sh

# Check help
./deployment/deploy.sh --help
```

### Status Check:
```bash
php artisan app:setup-production --check-only
```

## 🆘 Troubleshooting

### Common issues:

1. **500 Error**
   ```bash
   # Check logs
   tail -f storage/logs/laravel.log
   
   # Check permissions
   sudo chown -R www-data:www-data storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   ```

2. **Queue not working**
   ```bash
   # Check supervisor
   sudo supervisorctl status dartshop-worker:*
   
   # Restart worker
   sudo supervisorctl restart dartshop-worker:*
   ```

3. **Cache issues**
   ```bash
   # Clear cache
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

## 📞 Contact

If you encounter any issues, contact the development team.

---

**Last update**: $(date)
**Version**: 1.0.0 