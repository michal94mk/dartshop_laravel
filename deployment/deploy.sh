#!/bin/bash

# DartShop Production Deployment Script
# Usage: ./deploy.sh [--first-time] [--no-backup] [--skip-build] [--env=production]

set -e  # Exit on any error

# Parse command line arguments
FIRST_TIME=false
NO_BACKUP=false
SKIP_BUILD=false
ENVIRONMENT="production"

while [[ $# -gt 0 ]]; do
    case $1 in
        --first-time)
            FIRST_TIME=true
            shift
            ;;
        --no-backup)
            NO_BACKUP=true
            shift
            ;;
        --skip-build)
            SKIP_BUILD=true
            shift
            ;;
        --env=*)
            ENVIRONMENT="${1#*=}"
            shift
            ;;
        *)
            echo "Unknown option $1"
            echo "Usage: $0 [--first-time] [--no-backup] [--skip-build] [--env=production]"
            exit 1
            ;;
    esac
done

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration - can be overridden by environment variables
PROJECT_DIR="${PROJECT_DIR:-/var/www/html/dartshop_laravel}"
BACKUP_DIR="${BACKUP_DIR:-/var/backups/dartshop}"
PHP_VERSION="${PHP_VERSION:-8.2}"
NODE_VERSION="${NODE_VERSION:-20}"
GIT_REPO="${GIT_REPO:-https://github.com/michal94mk/dartshop_laravel}"
DOMAIN="${DOMAIN:-localhost}"

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

# Check if running as root and system requirements
check_permissions() {
    if [[ $EUID -eq 0 ]]; then
        log_error "This script should not be run as root"
        log_info "Please run as a regular user with sudo privileges"
        exit 1
    fi
    
    # Check if user has sudo privileges
    if ! sudo -n true 2>/dev/null; then
        log_info "This script requires sudo privileges. Please enter your password when prompted."
    fi
    
    # Check if project directory exists, create if not
    if [ ! -d "$PROJECT_DIR" ]; then
        log_info "Project directory doesn't exist. Creating: $PROJECT_DIR"
        sudo mkdir -p "$PROJECT_DIR"
        sudo chown $USER:$USER "$PROJECT_DIR"
    fi
    
    # Check system requirements
    check_system_requirements
}

# Check system requirements
check_system_requirements() {
    log_info "Checking system requirements..."
    
    local missing_packages=()
    
    # Check PHP
    if ! command -v php &> /dev/null; then
        missing_packages+=("php${PHP_VERSION}")
        missing_packages+=("php${PHP_VERSION}-fpm")
        missing_packages+=("php${PHP_VERSION}-mysql")
        missing_packages+=("php${PHP_VERSION}-curl")
        missing_packages+=("php${PHP_VERSION}-mbstring")
        missing_packages+=("php${PHP_VERSION}-xml")
        missing_packages+=("php${PHP_VERSION}-zip")
        missing_packages+=("php${PHP_VERSION}-gd")
        missing_packages+=("php${PHP_VERSION}-bcmath")
        # Note: php${PHP_VERSION}-redis not needed as Redis is not currently used
    fi
    
    # Check Composer
    if ! command -v composer &> /dev/null; then
        log_warning "Composer not found. Installing..."
        install_composer
    fi
    
    # Check Node.js
    if ! command -v node &> /dev/null; then
        missing_packages+=("nodejs")
        missing_packages+=("npm")
    fi
    
    # Check Git
    if ! command -v git &> /dev/null; then
        missing_packages+=("git")
    fi
    
    # Check web server
    if ! command -v apache2 &> /dev/null && ! command -v nginx &> /dev/null; then
        log_warning "No web server found. Apache recommended."
        missing_packages+=("apache2")
    fi
    
    # Check MySQL
    if ! command -v mysql &> /dev/null; then
        log_warning "MySQL client not found."
        missing_packages+=("mysql-client")
    fi
    
    # Install missing packages
    if [ ${#missing_packages[@]} -gt 0 ]; then
        log_warning "Missing packages: ${missing_packages[*]}"
        read -p "Do you want to install missing packages? [Y/n]: " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Nn]$ ]]; then
            log_error "Required packages are missing. Please install them manually."
            exit 1
        fi
        
        log_info "Installing missing packages..."
        sudo apt update
        sudo apt install -y "${missing_packages[@]}"
    fi
    
    log_success "System requirements check completed"
}

# Install Composer
install_composer() {
    log_info "Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
    log_success "Composer installed"
}

# Create backup
create_backup() {
    if [ "$NO_BACKUP" = true ]; then
        log_warning "Skipping backup (--no-backup flag)"
        return
    fi
    
    if [ "$FIRST_TIME" = false ]; then
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
        
        # Keep only last 10 backups
        sudo find $BACKUP_DIR -name "backup-*.tar.gz" -type f | sort -r | tail -n +11 | xargs sudo rm -f
    fi
}

# Update code from git
update_code() {
    log_info "Updating code from repository..."
    
    # Ensure we're in the project directory
    if [ ! -d "$PROJECT_DIR" ]; then
        log_info "Creating project directory: $PROJECT_DIR"
        sudo mkdir -p "$PROJECT_DIR"
        sudo chown $USER:$USER "$PROJECT_DIR"
    fi
    
    cd $PROJECT_DIR
    
    # Check if we're in a git repository
    if ! git rev-parse --git-dir > /dev/null 2>&1; then
        log_info "No git repository found. Cloning from: $GIT_REPO"
        
        # Remove any existing files (if directory exists but no git)
        if [ "$(ls -A .)" ]; then
            log_warning "Directory not empty. Moving existing files to backup..."
            sudo mkdir -p /tmp/dartshop_backup_$(date +%s)
            sudo mv * /tmp/dartshop_backup_$(date +%s)/ 2>/dev/null || true
        fi
        
        # Clone repository
        if git clone $GIT_REPO .; then
            log_success "Repository cloned successfully"
        else
            log_error "Failed to clone repository: $GIT_REPO"
            log_error "Please check your internet connection and repository URL"
            exit 1
        fi
        
        return
    fi
    
    # Check if remote origin exists
    if ! git remote get-url origin > /dev/null 2>&1; then
        log_info "Adding remote origin..."
        git remote add origin $GIT_REPO
    fi
    
    # Check current branch
    local current_branch=$(git branch --show-current)
    log_info "Current branch: $current_branch"
    
    # Check if there are any uncommitted changes
    if ! git diff-index --quiet HEAD -- 2>/dev/null; then
        log_warning "Uncommitted changes found. Stashing..."
        git stash push -m "Auto-stash before deployment $(date)"
    fi
    
    # Check remote connection
    log_info "Checking remote connection..."
    if ! timeout 30 git ls-remote --exit-code origin > /dev/null 2>&1; then
        log_error "Cannot connect to remote repository: $GIT_REPO"
        log_error "Please check your internet connection and repository URL"
        exit 1
    fi
    
    # Fetch latest changes
    log_info "Fetching latest changes..."
    git fetch origin
    
    # Pull latest changes
    log_info "Pulling latest changes from origin/master..."
    if git reset --hard origin/master; then
        log_success "Code updated successfully"
        
        # Show last commit info
        local last_commit=$(git log -1 --oneline)
        log_info "Latest commit: $last_commit"
    else
        log_error "Failed to update code from repository"
        exit 1
    fi
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
    if [ "$SKIP_BUILD" = true ]; then
        log_warning "Skipping asset build (--skip-build flag)"
        return
    fi
    
    log_info "Building production assets..."
    cd $PROJECT_DIR
    npm run build
    
    log_success "Assets built"
}

# Run migrations and database setup
run_migrations() {
    log_info "Setting up database..."
    cd $PROJECT_DIR
    
    # Check database connection
    if ! check_database_connection; then
        log_error "Cannot connect to database!"
        setup_database_instructions
        return 1
    fi
    
    # Run migrations
    log_info "Running database migrations..."
    if php artisan migrate --force; then
        log_success "Migrations completed successfully"
    else
        log_error "Migration failed!"
        return 1
    fi
    
    # Ask about seeding for first-time deployment
    if [ "$FIRST_TIME" = true ]; then
        log_info "Setting up initial data..."
        
        # Check what seeders are available
        local available_seeders=$(php artisan db:seed --class=nonexistent 2>&1 | grep "Class" | cut -d' ' -f2 || echo "")
        
        read -p "Do you want to seed the database with initial data? [Y/n]: " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Nn]$ ]]; then
            log_info "Seeding database..."
            php artisan db:seed --force
            log_success "Database seeded successfully"
        fi
    fi
}

# Check database connection
check_database_connection() {
    log_info "Testing database connection..."
    
    if php artisan tinker --execute="
        try {
            DB::connection()->getPdo();
            echo 'Database connection successful';
        } catch (Exception \$e) {
            echo 'Database connection failed: ' . \$e->getMessage();
            exit(1);
        }
    " > /dev/null 2>&1; then
        log_success "Database connection successful"
        return 0
    else
        return 1
    fi
}

# Display database setup instructions
setup_database_instructions() {
    log_warning "Database connection failed!"
    log_info "Please ensure your database is properly configured:"
    echo ""
    echo "1. Create MySQL database and user:"
    echo "   sudo mysql -u root -p"
    echo "   CREATE DATABASE dartshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    echo "   CREATE USER 'dartshop'@'localhost' IDENTIFIED BY 'your_password';"
    echo "   GRANT ALL PRIVILEGES ON dartshop.* TO 'dartshop'@'localhost';"
    echo "   FLUSH PRIVILEGES;"
    echo "   EXIT;"
    echo ""
    echo "2. Update your .env file with correct database credentials:"
    echo "   DB_DATABASE=dartshop"
    echo "   DB_USERNAME=dartshop"
    echo "   DB_PASSWORD=your_password"
    echo ""
    
    read -p "Do you want to edit .env file now? [Y/n]: " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Nn]$ ]]; then
        ${EDITOR:-nano} .env
        
        # Test connection again
        log_info "Testing database connection again..."
        if check_database_connection; then
            return 0
        else
            log_error "Database connection still failing. Please check your configuration."
            return 1
        fi
    fi
    
    return 1
}

# Setup production configuration and environment
setup_production() {
    log_info "Setting up production configuration..."
    cd $PROJECT_DIR
    
    # Setup .env file
    setup_env_file
    
    # Generate app key if not exists or empty
    if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
        log_info "Generating application key..."
        php artisan key:generate --force
    fi
    
    # Create storage link
    log_info "Creating storage link..."
    php artisan storage:link
    
    # Run production setup command if exists
    if php artisan list | grep -q "app:setup-production"; then
        log_info "Running production setup command..."
        php artisan app:setup-production
    fi
    
    # Optimize application
    log_info "Optimizing application..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    log_success "Production configuration completed"
}

# Setup .env file
setup_env_file() {
    if [ ! -f .env ]; then
        log_info "Creating .env file from template..."
        
        if [ -f .env.example ]; then
            cp .env.example .env
        else
            log_warning ".env.example not found. Creating basic .env file..."
            create_basic_env_file
        fi
        
        log_warning "⚠️  IMPORTANT: Please configure your .env file!"
        log_info "Required settings:"
        echo "  • APP_URL=https://$DOMAIN"
        echo "  • Database connection (DB_*)"
        echo "  • Mail settings (MAIL_*)"
        echo "  • Any API keys (Stripe, Google, etc.)"
        echo ""
        
        if [ "$FIRST_TIME" = true ]; then
            read -p "Do you want to edit .env file now? [Y/n]: " -n 1 -r
            echo
            if [[ ! $REPLY =~ ^[Nn]$ ]]; then
                ${EDITOR:-nano} .env
            fi
        fi
    else
        log_info ".env file already exists"
        
        # Update APP_URL if domain is provided
        if [ "$DOMAIN" != "localhost" ] && grep -q "APP_URL=" .env; then
            log_info "Updating APP_URL to: https://$DOMAIN"
            sed -i "s|APP_URL=.*|APP_URL=https://$DOMAIN|" .env
        fi
    fi
}

# Create basic .env file
create_basic_env_file() {
    cat > .env << EOF
APP_NAME="DartShop"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://$DOMAIN

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dartshop
DB_USERNAME=dartshop
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@$DOMAIN
MAIL_FROM_NAME="DartShop"
EOF
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
    
    # Restart Apache
    sudo systemctl restart apache2
    
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
    local url="http://$DOMAIN"
    if [ "$DOMAIN" != "localhost" ]; then
        url="https://$DOMAIN"
    fi
    
    if curl -f -s -o /dev/null "$url"; then
        log_success "Website is responding ($url)"
    else
        log_warning "Website check failed for $url, trying localhost..."
        if curl -f -s -o /dev/null "http://localhost"; then
            log_success "Website is responding on localhost"
        else
            log_error "Website is not responding"
            return 1
        fi
    fi
    
    # Check database connection
    cd $PROJECT_DIR
    if php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK';" > /dev/null 2>&1; then
        log_success "Database connection OK"
    else
        log_error "Database connection failed"
        return 1
    fi
    
    # Check .env file
    if [ ! -f .env ]; then
        log_error ".env file not found"
        return 1
    fi
    
    # Check storage link
    if [ ! -L "public/storage" ]; then
        log_warning "Storage link not found, creating..."
        php artisan storage:link
    fi
    
    log_success "Health check completed"
}

# Main deployment flow
main() {
    log_info "Starting deployment process for environment: $ENVIRONMENT"
    log_info "Configuration:"
    echo "  • Project directory: $PROJECT_DIR"
    echo "  • Backup directory: $BACKUP_DIR"
    echo "  • Domain: $DOMAIN"
    echo "  • First time: $FIRST_TIME"
    echo "  • Skip backup: $NO_BACKUP"
    echo "  • Skip build: $SKIP_BUILD"
    
    check_permissions
    create_backup
    update_code
    install_dependencies
    build_assets
    
    if [ "$FIRST_TIME" = true ]; then
        log_info "First time deployment - running migrations automatically"
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
    
    if ! health_check; then
        log_error "Health check failed! Please check logs and fix issues."
        exit 1
    fi
    
    log_success "🎉 Deployment completed successfully!"
    echo ""
    log_info "📋 Post-deployment checklist:"
    echo "✅ Application deployed to: $PROJECT_DIR"
    echo "✅ Git repository: $GIT_REPO"
    echo "✅ Environment: $ENVIRONMENT"
    echo ""
    
    # Display URLs
    local http_url="http://$DOMAIN"
    local https_url="https://$DOMAIN"
    if [ "$DOMAIN" = "localhost" ]; then
        http_url="http://localhost"
        https_url="http://localhost (configure domain for HTTPS)"
    fi
    
    log_info "🌐 Access your application:"
    echo "   • HTTP:  $http_url"
    echo "   • HTTPS: $https_url"
    echo ""
    
    log_info "🔧 Next steps to complete setup:"
    echo "1. Configure web server:"
    echo "   Apache (recommended):"
    echo "     sudo cp $PROJECT_DIR/deployment/apache.conf /etc/apache2/sites-available/dartshop.conf"
    echo "     sudo a2ensite dartshop.conf && sudo a2enmod rewrite headers deflate expires ssl"
    echo "     sudo apache2ctl configtest && sudo systemctl reload apache2"
    echo ""
    echo "   Nginx (alternative):"
    echo "     sudo cp $PROJECT_DIR/deployment/nginx.conf /etc/nginx/sites-available/dartshop"
    echo "     sudo ln -s /etc/nginx/sites-available/dartshop /etc/nginx/sites-enabled/"
    echo "     sudo nginx -t && sudo systemctl reload nginx"
    echo ""
    echo "2. Setup SSL certificate:"
    echo "   sudo apt install certbot python3-certbot-apache"
    echo "   sudo certbot --apache -d $DOMAIN"
    echo ""
    echo "3. Configure cron jobs:"
    echo "   sudo crontab -e"
    echo "   # Add: * * * * * cd $PROJECT_DIR && php artisan schedule:run >> /dev/null 2>&1"
    echo ""
    echo "4. Setup queue worker (optional):"
    echo "   sudo cp $PROJECT_DIR/deployment/supervisor.conf /etc/supervisor/conf.d/dartshop.conf"
    echo "   sudo supervisorctl reread && sudo supervisorctl update"
    echo ""
    
    log_info "🔍 Monitoring commands:"
    echo "   • Application logs: tail -f $PROJECT_DIR/storage/logs/laravel.log"
    echo "   • Web server logs: tail -f /var/log/nginx/dartshop_error.log"
    echo "   • Queue status: sudo supervisorctl status dartshop-worker:*"
    echo "   • System status: systemctl status nginx php${PHP_VERSION}-fpm"
    echo ""
    
    log_info "🚨 Important reminders:"
    echo "   • Test all functionality thoroughly"
    echo "   • Setup monitoring and backups"
    echo "   • Review security settings"
    echo "   • Update DNS records if needed"
    echo ""
    
    if [ "$FIRST_TIME" = true ]; then
        log_warning "⚠️  First-time deployment notes:"
        echo "   • Configure your .env file completely"
        echo "   • Set up email delivery (SMTP)"
        echo "   • Configure payment gateways (Stripe)"
        echo "   • Set up social login (Google OAuth)"
        echo "   • Test the complete user journey"
    fi
}

# Show help if requested
if [ "$1" = "--help" ] || [ "$1" = "-h" ]; then
    echo "DartShop Deployment Script"
    echo ""
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --first-time     First time deployment (runs migrations automatically)"
    echo "  --no-backup      Skip creating backup"
    echo "  --skip-build     Skip building frontend assets"
    echo "  --env=ENV        Set environment (default: production)"
    echo "  --help, -h       Show this help message"
    echo ""
    echo "Environment variables:"
    echo "  PROJECT_DIR      Project directory (default: /var/www/dartshop_laravel)"
    echo "  BACKUP_DIR       Backup directory (default: /var/backups/dartshop)"
    echo "  DOMAIN          Domain name (default: localhost)"
    echo "  PHP_VERSION     PHP version (default: 8.2)"
    echo "  GIT_REPO        Git repository URL"
    echo ""
    echo "Examples:"
    echo "  $0 --first-time                    # First deployment"
    echo "  $0                                 # Regular deployment"
    echo "  $0 --no-backup --skip-build       # Quick deployment"
    echo "  DOMAIN=example.com $0              # Set domain"
    exit 0
fi

# Run main function
main 