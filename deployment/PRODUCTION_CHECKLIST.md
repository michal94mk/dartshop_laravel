# ✅ Production Deployment Checklist - DartShop

## 🔐 Security & Configuration

### Environment Variables (.env)
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY` - strong, generated key
- [ ] `APP_URL` - correct HTTPS production URL
- [ ] Database - production credentials
- [ ] Redis - configured (if used)
- [ ] SMTP - working email settings
- [ ] Stripe - production keys (`pk_live_`, `sk_live_`)
- [ ] Google OAuth - production keys
- [ ] reCAPTCHA - production keys

### Cache & Queue Configuration
- [ ] `CACHE_DRIVER=redis` (or other production-ready driver)
- [ ] `QUEUE_CONNECTION=database` (or redis)
- [ ] `SESSION_DRIVER=redis` (or database)

## 🗄️ Database

- [ ] Database `dartshop` created with correct charset (utf8mb4)
- [ ] Database user with proper privileges
- [ ] Migrations run: `php artisan migrate --force`
- [ ] Seeded basic data:
  - [ ] Roles and permissions
  - [ ] Product categories
  - [ ] Brands
  - [ ] About Us, Privacy Policy, Terms of Service

## 🚀 Build & Optimization

- [ ] PHP dependencies installed: `composer install --no-dev --optimize-autoloader`
- [ ] JS dependencies installed: `npm ci`
- [ ] Assets built: `npm run build`
- [ ] Cache optimized:
  - [ ] `php artisan config:cache`
  - [ ] `php artisan route:cache`
  - [ ] `php artisan view:cache`
  - [ ] `php artisan event:cache`

## 🌐 Web Server

- [ ] Nginx/Apache configured
- [ ] SSL certificate installed and working
- [ ] HTTPS redirection enabled
- [ ] Security headers added
- [ ] Gzip compression enabled
- [ ] File upload limits set (min. 10MB)
- [ ] Static files caching configured

## 📁 File Permissions

- [ ] `storage/` - 775, owner www-data
- [ ] `bootstrap/cache/` - 775, owner www-data
- [ ] `storage/logs/` - 775, owner www-data
- [ ] `php artisan storage:link` run

## 🔄 Queue & Cron

- [ ] Supervisor configured for queue worker
- [ ] Queue worker running and healthy
- [ ] Cron scheduler configured: `* * * * * cd /path && php artisan schedule:run`
- [ ] Backup cron job configured

## 📧 Email & Notifications

- [ ] SMTP works - test email sent
- [ ] Email templates render correctly
- [ ] Contact form sends emails to admin
- [ ] Newsletter subscription works
- [ ] Email verification works

## 💳 Payments & Integrations

- [ ] Stripe - production keys configured
- [ ] Stripe webhooks configured and working
- [ ] Google OAuth - Google login works
- [ ] reCAPTCHA - works on forms
- [ ] Test transactions - full payment flow works

## 🔍 Functional Testing

### Frontend
- [ ] Homepage loads correctly
- [ ] Product list & filtering works
- [ ] Product details display
- [ ] Cart works (add/remove)
- [ ] Checkout process works
- [ ] User registration
- [ ] Login/logout
- [ ] User panel
- [ ] Favorite products

### Admin Panel
- [ ] Admin panel login
- [ ] Product management
- [ ] Category & brand management
- [ ] Order management
- [ ] User management
- [ ] Statistics & reports

### API
- [ ] All API endpoints work
- [ ] API authentication (Sanctum)
- [ ] Rate limiting configured
- [ ] CORS headers correct

## 🛡️ Security

- [ ] Firewall configured (only 80, 443, 22 open)
- [ ] PHP - `expose_php=Off`, `display_errors=Off`
- [ ] Nginx - `server_tokens off`
- [ ] Sensitive config files hidden (.env, etc.)
- [ ] SQL injection protection (use Eloquent)
- [ ] XSS protection (Content Security Policy)
- [ ] CSRF protection enabled

## 📊 Monitoring & Logs

- [ ] Laravel logs written correctly
- [ ] Log rotation configured
- [ ] Error monitoring (Sentry?) configured
- [ ] Uptime monitoring configured
- [ ] Performance monitoring
- [ ] Backup system configured

## 🚦 Performance Testing

- [ ] Page loads < 3 seconds
- [ ] Database queries optimized
- [ ] Images optimized
- [ ] CDN configured (if needed)
- [ ] Cache warming performed

## 📋 Documentation

- [ ] Up-to-date API documentation
- [ ] Admin instructions
- [ ] Backup & recovery procedures
- [ ] Support contact info

## 🎯 Final Checks

- [ ] DNS points to production server
- [ ] Domain certificates valid
- [ ] All links work
- [ ] Forms work correctly
- [ ] Search functionality works
- [ ] Mobile responsiveness
- [ ] Cross-browser compatibility

## 🆘 Rollback Plan

- [ ] Database backup before deployment
- [ ] Previous code version backup
- [ ] Rollback procedure documented
- [ ] Test rollback procedure

---

**Date checked**: _______________
**Checked by**: _______________
**Version**: _______________

## 📝 Notes

```
[Space for notes and comments]
``` 