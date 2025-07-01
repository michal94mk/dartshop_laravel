# 🛒 DartShop - Modern E-commerce Platform

DartShop is a full-featured e-commerce platform built with Laravel and Vue.js, offering a complete online shopping experience with modern features and robust architecture.

## ✨ Features

### 🔐 Authentication & Authorization
- **Multi-authentication system**: Traditional email/password and Google OAuth
- **Role-based access control**: Customer and Admin roles with permissions
- **Email verification**: Automated email verification with queue processing
- **Password management**: Secure password reset and change functionality

### 🛍️ E-commerce Core
- **Product management**: Categories, brands, detailed product pages
- **Shopping cart**: Persistent cart with quantity management
- **Favorites system**: Wishlist functionality for registered users
- **Order management**: Complete order lifecycle with status tracking
- **Reviews system**: Product reviews with admin moderation

### 💳 Payment & Checkout
- **Stripe integration**: Secure payment processing
- **Guest checkout**: Purchase without registration
- **Multiple payment methods**: Cards, digital wallets
- **Order confirmation**: Automated email notifications

### 📧 Email System
- **Queue-based processing**: Reliable email delivery with Laravel Queues
- **SMTP integration**: Brevo (Sendinblue) for professional email delivery
- **Automated notifications**: Order confirmations, status updates, newsletters
- **Email verification**: Account activation via email

### 🎨 User Interface
- **Responsive design**: Mobile-first approach with Tailwind CSS
- **Modern Vue.js frontend**: Interactive and dynamic user experience
- **Success notifications**: Real-time feedback for user actions
- **Admin dashboard**: Comprehensive management interface

### 🔧 Technical Features
- **Laravel 12**: Latest Laravel framework with modern PHP practices
- **Vue.js 3**: Composition API with reactive state management
- **Database**: MySQL with optimized migrations and relationships
- **File storage**: Image handling with Intervention Image
- **Security**: CSRF protection, XSS prevention, secure authentication

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Node.js 18+
- MySQL 8.0+
- Composer
- Git

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/your-username/dartshop.git
cd dartshop
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node.js dependencies**
```bash
npm install
```

4. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure your `.env` file**
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dartshop
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Email (Brevo/Sendinblue)
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your_brevo_email
MAIL_PASSWORD=your_brevo_password
MAIL_ENCRYPTION=tls

# Stripe
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key

# Queue
QUEUE_CONNECTION=database
```

6. **Database setup**
```bash
php artisan migrate
php artisan db:seed
```

7. **Build frontend assets**
```bash
npm run build
```

## 🏃‍♂️ Running the Application

1. **Start the Laravel development server**
```bash
php artisan serve
```

2. **Start the queue worker** (for email processing)
```bash
php artisan queue:work --queue=emails,default --tries=3 --timeout=120
```

3. **For development with hot reload**
```bash
npm run dev
```

The application will be available at `http://localhost:8000`

## 📋 Default Credentials

After seeding, you can log in with:

**Admin Account:**
- Email: `admin@dartshop.com`
- Password: `password`

**Test Customer:**
- Email: `customer@dartshop.com`
- Password: `password`

## 🗂️ Project Structure

```
dartshop/
├── app/
│   ├── Http/Controllers/       # API & Web Controllers
│   ├── Models/                 # Eloquent Models
│   ├── Services/               # Business Logic
│   ├── Mail/                   # Mail Classes
│   └── Observers/              # Model Observers
├── database/
│   ├── migrations/             # Database Migrations
│   └── seeders/                # Database Seeders
├── resources/
│   ├── js/
│   │   ├── components/         # Vue Components
│   │   ├── pages/              # Vue Pages
│   │   ├── stores/             # Pinia State Management
│   │   └── router/             # Vue Router
│   └── views/                  # Blade Templates
└── routes/
    ├── api.php                 # API Routes
    └── web.php                 # Web Routes
```

## 🔧 Key Technologies

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Vue.js 3, Tailwind CSS
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum, Laravel Socialite
- **Payments**: Stripe
- **Email**: Laravel Mail, Brevo SMTP
- **State Management**: Pinia
- **Build Tools**: Vite
- **Testing**: PHPUnit, Laravel Dusk

## 📦 Main Dependencies

```json
{
  "php": "^8.2",
  "laravel/framework": "^12.0",
  "laravel/socialite": "*",
  "spatie/laravel-permission": "^6.0",
  "stripe/stripe-php": "^17.2",
  "intervention/image": "^2.7"
}
```

## 🚀 Deployment

### Production Setup

1. **Server requirements**
   - PHP 8.2+ with required extensions
   - MySQL 8.0+
   - Node.js for asset compilation
   - SSL certificate for HTTPS

2. **Environment configuration**
   - Set `APP_ENV=production`
   - Set `APP_DEBUG=false`
   - Configure production database
   - Set up real SMTP credentials
   - Configure production OAuth credentials

3. **Optimization commands**
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

4. **Queue processing**
   - Set up a process manager (Supervisor) for queue workers
   - Configure cron jobs for scheduled tasks

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

If you encounter any issues or have questions:

1. Check the [Issues](https://github.com/your-username/dartshop/issues) page
2. Create a new issue with detailed information
3. Include error messages and steps to reproduce

## 🎯 Roadmap

- [ ] Multi-language support
- [ ] Advanced inventory management
- [ ] Subscription products
- [ ] Mobile app (React Native)
- [ ] Advanced analytics dashboard
- [ ] Social media integration

---

**DartShop** - Building the future of e-commerce, one feature at a time. 🚀
