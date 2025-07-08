#!/bin/bash

# DartShop Production Deployment Script
# Usage: ./deploy.sh [--first-time]

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR="/var/www/html/dartshop_laravel"
BACKUP_DIR="/var/backups/dartshop"
PHP_VERSION="8.2"
NODE_VERSION="20"

# Functions
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
check_permissions() {
    if [[ $EUID -eq 0 ]]; then
        log_error "This script should not be run as root"
        exit 1
    fi
}

# Create backup
create_backup() {
    if [ "$1" != "--first-time" ]; then
        log_info "Creating backup..."
        sudo mkdir -p $BACKUP_DIR
        BACKUP_FILE="$BACKUP_DIR/backup-$(date +%Y%m%d-%H%M%S).tar.gz"
        
        cd $PROJECT_DIR
        sudo tar -czf $BACKUP_FILE \
            --exclude='node_modules' \
            --exclude='vendor' \
            --exclude='storage/logs/*' \
            --exclude='storage/framework/cache/*' \
            --exclude='storage/framework/sessions/*' \
            --exclude='storage/framework/views/*' \
            .
        
        log_success "Backup created: $BACKUP_FILE"
    fi
}

# Update code from git
update_code() {
    log_info "Updating code from repository..."
    cd $PROJECT_DIR
    
    # Stash any local changes
    git stash
    
    # Pull latest changes
    git pull origin master
    
    log_success "Code updated"
}

# Install dependencies
install_dependencies() {
    log_info "Installing PHP dependencies..."
    cd $PROJECT_DIR
    composer install --optimize-autoloader --no-dev --no-interaction
    
    log_info "Installing Node.js dependencies..."
    npm ci --only=production
    
    log_success "Dependencies installed"
}

# Build assets
build_assets() {
    log_info "Building production assets..."
    cd $PROJECT_DIR
    npm run build
    
    log_success "Assets built"
}

# Run migrations
run_migrations() {
    log_info "Running database migrations..."
    cd $PROJECT_DIR
    php artisan migrate --force
    
    log_success "Migrations completed"
}

# Setup production configuration
setup_production() {
    log_info "Setting up production configuration..."
    cd $PROJECT_DIR
    
    # Generate app key if not exists
    if ! grep -q "APP_KEY=" .env 2>/dev/null; then
        php artisan key:generate --force
    fi
    
    # Create storage link
    php artisan storage:link
    
    # Run production setup command
    php artisan app:setup-production
    
    log_success "Production configuration completed"
}

# Set file permissions
set_permissions() {
    log_info "Setting file permissions..."
    cd $PROJECT_DIR
    
    # Set ownership
    sudo chown -R www-data:www-data .
    sudo chown -R $USER:www-data storage bootstrap/cache
    
    # Set permissions
    sudo chmod -R 755 .
    sudo chmod -R 775 storage bootstrap/cache
    sudo chmod -R 664 storage/logs
    
    log_success "Permissions set"
}

# Restart services
restart_services() {
    log_info "Restarting services..."
    
    # Restart PHP-FPM
    sudo systemctl restart php${PHP_VERSION}-fpm
    
    # Restart Nginx
    sudo systemctl restart nginx
    
    # Restart queue worker (if using supervisor)
    if command -v supervisorctl &> /dev/null; then
        sudo supervisorctl restart dartshop-worker:*
    fi
    
    log_success "Services restarted"
}

# Cleanup
cleanup() {
    log_info "Cleaning up..."
    cd $PROJECT_DIR
    
    # Clear old logs
    sudo find storage/logs -name "*.log" -type f -mtime +7 -delete
    
    # Clear old cache files
    php artisan cache:clear
    php artisan view:clear
    
    log_success "Cleanup completed"
}

# Health check
health_check() {
    log_info "Running health check..."
    
    # Check if website is responding
    if curl -f -s http://localhost > /dev/null; then
        log_success "Website is responding"
    else
        log_error "Website is not responding"
        exit 1
    fi
    
    # Check database connection
    cd $PROJECT_DIR
    if php artisan tinker --execute="DB::connection()->getPdo();" > /dev/null 2>&1; then
        log_success "Database connection OK"
    else
        log_error "Database connection failed"
        exit 1
    fi
}

# Main deployment flow
main() {
    local first_time_flag="$1"
    
    log_info "Starting deployment process..."
    
    check_permissions
    create_backup "$first_time_flag"
    update_code
    install_dependencies
    build_assets
    
    if [ "$first_time_flag" == "--first-time" ]; then
        run_migrations
    else
        # Ask for migration confirmation
        read -p "Run database migrations? [y/N]: " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            run_migrations
        fi
    fi
    
    setup_production
    set_permissions
    restart_services
    cleanup
    health_check
    
    log_success "Deployment completed successfully!"
    log_info "Don't forget to:"
    echo "  • Update DNS records if needed"
    echo "  • Test all functionality"
    echo "  • Monitor error logs"
    echo "  • Setup monitoring alerts"
}

# Run main function with all arguments
main "$@" 