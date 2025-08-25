# DartShop Deployment Configuration
# Source this file before running deploy.sh: source deployment/config.sh

# Basic configuration
export PROJECT_DIR="/var/www/dartshop_laravel"
export BACKUP_DIR="/var/backups/dartshop"
export DOMAIN="your-domain.com"

# Server configuration
export PHP_VERSION="8.2"
export NODE_VERSION="20"

# Git configuration
export GIT_REPO="https://github.com/michal94mk/dartshop_laravel"
export GIT_BRANCH="master"

# Database configuration (for backup script)
export DB_NAME="dartshop"
export DB_USER="dartshop"
export DB_PASSWORD="your_db_password"

# Email for notifications (optional)
export ADMIN_EMAIL="admin@your-domain.com"

# Slack webhook for notifications (optional)
# export SLACK_WEBHOOK="https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK"

echo "✅ DartShop deployment configuration loaded"
echo "Domain: $DOMAIN"
echo "Project dir: $PROJECT_DIR"
