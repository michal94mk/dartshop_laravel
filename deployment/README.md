# 🚀 DartShop - Production Deployment Guide

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
- **Redis**: 6.0+ (optional but recommended)
- **Supervisor**: for queue worker

## 🛠️ Deployment Steps

### 1. Prepare the Server

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

# Install Redis (optional)
sudo apt install -y redis-server

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

### 3. Clone and Configure the Application

```bash
# Clone the repository
sudo mkdir -p /var/www
cd /var/www
sudo git clone https://github.com/your-repo/dartshop.git dartshop
cd dartshop

# Set ownership
sudo chown -R $USER:www-data /var/www/dartshop

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

CACHE_DRIVER=redis
QUEUE_CONNECTION=database
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=
REDIS_PORT=6379

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

```bash
# Run installation
chmod +x deployment/deploy.sh
./deployment/deploy.sh --first-time

# Or manually:
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

## 🔄 Updates

```bash
# Standard deployment
./deployment/deploy.sh

# Check status
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