#!/bin/bash

# DartShop Server Setup Script
# This script prepares a fresh Ubuntu server for DartShop deployment
# Usage: curl -fsSL https://raw.githubusercontent.com/michal94mk/dartshop_laravel/master/deployment/server-setup.sh | bash

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
PHP_VERSION="8.2"
NODE_VERSION="20"

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [[ $EUID -eq 0 ]]; then
    log_error "This script should not be run as root"
    log_info "Please run as a regular user with sudo privileges"
    exit 1
fi

log_info "🚀 Starting DartShop server setup..."
echo ""

# Update system
log_info "Updating system packages..."
sudo apt update && sudo apt upgrade -y

# Install basic packages
log_info "Installing basic packages..."
sudo apt install -y curl wget git unzip software-properties-common apt-transport-https ca-certificates gnupg lsb-release

# Install PHP
log_info "Installing PHP ${PHP_VERSION}..."
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y \
    php${PHP_VERSION} \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-redis \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-soap \
    php${PHP_VERSION}-imagick

# Install Composer
log_info "Installing Composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Install Node.js
log_info "Installing Node.js ${NODE_VERSION}..."
curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | sudo -E bash -
sudo apt install -y nodejs

# Install Nginx
log_info "Installing Nginx..."
sudo apt install -y nginx

# Install MySQL
log_info "Installing MySQL..."
sudo apt install -y mysql-server

# Redis is not currently used in this project
# log_info "Installing Redis..."
# sudo apt install -y redis-server

# Install Supervisor
log_info "Installing Supervisor..."
sudo apt install -y supervisor

# Configure PHP
log_info "Configuring PHP..."
sudo sed -i "s/upload_max_filesize = .*/upload_max_filesize = 10M/" /etc/php/${PHP_VERSION}/fpm/php.ini
sudo sed -i "s/post_max_size = .*/post_max_size = 10M/" /etc/php/${PHP_VERSION}/fpm/php.ini
sudo sed -i "s/max_execution_time = .*/max_execution_time = 300/" /etc/php/${PHP_VERSION}/fpm/php.ini
sudo sed -i "s/memory_limit = .*/memory_limit = 256M/" /etc/php/${PHP_VERSION}/fpm/php.ini

# Start services
log_info "Starting services..."
sudo systemctl enable nginx php${PHP_VERSION}-fpm mysql supervisor
sudo systemctl start nginx php${PHP_VERSION}-fpm mysql supervisor

# Configure firewall
log_info "Configuring firewall..."
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw --force enable

# Secure MySQL installation
log_info "Securing MySQL installation..."
log_warning "Please follow the MySQL security setup prompts..."
sudo mysql_secure_installation

# Create project directory
log_info "Creating project directory..."
sudo mkdir -p /var/www/dartshop_laravel
sudo chown $USER:www-data /var/www/dartshop_laravel
sudo chmod 755 /var/www/dartshop_laravel

# Create backup directory
log_info "Creating backup directory..."
sudo mkdir -p /var/backups/dartshop
sudo chown $USER:$USER /var/backups/dartshop

log_success "🎉 Server setup completed!"
echo ""
log_info "📋 What was installed:"
echo "✅ PHP ${PHP_VERSION} with extensions"
echo "✅ Composer"
echo "✅ Node.js ${NODE_VERSION} & npm"
echo "✅ Nginx web server"
echo "✅ MySQL database"
echo "✅ Supervisor process manager"
echo "✅ UFW firewall configured"
echo ""

log_info "🔧 Next steps:"
echo "1. Configure MySQL database:"
echo "   sudo mysql -u root -p"
echo "   CREATE DATABASE dartshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo "   CREATE USER 'dartshop'@'localhost' IDENTIFIED BY 'strong_password';"
echo "   GRANT ALL PRIVILEGES ON dartshop.* TO 'dartshop'@'localhost';"
echo "   FLUSH PRIVILEGES;"
echo "   EXIT;"
echo ""
echo "2. Deploy DartShop application:"
echo "   cd /var/www/dartshop_laravel"
echo "   git clone https://github.com/michal94mk/dartshop_laravel.git ."
echo "   chmod +x deployment/deploy.sh"
echo "   DOMAIN=your-domain.com ./deployment/deploy.sh --first-time"
echo ""
echo "3. Configure DNS to point to this server"
echo ""

log_warning "⚠️  Important notes:"
echo "• Save your MySQL root password safely"
echo "• Choose a strong password for the dartshop database user"
echo "• Update your domain name in the deployment command"
echo "• Consider setting up automated backups"
echo ""

log_info "Server is ready for DartShop deployment! 🚀"
