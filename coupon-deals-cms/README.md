# 🎯 Coupon Deals & Affiliate Product CMS

A comprehensive Laravel-based Content Management System designed specifically for managing coupons, deals, and affiliate products with a modern Bootstrap UI and advanced features.

![Laravel](https://img.shields.io/badge/Laravel-v10.0-red.svg)
![PHP](https://img.shields.io/badge/PHP-v8.1+-blue.svg)
![Bootstrap](https://img.shields.io/badge/Bootstrap-v5.3-purple.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## ✨ Features

### 🚀 Core Features
- **Modern UI Design** - Clean, responsive Bootstrap 5 interface
- **Coupon Management** - Complete coupon lifecycle management
- **Deal Management** - Hot deals with "Get Deal" buttons
- **Product Management** - Product catalog with "Check Product" buttons
- **Store Management** - Store profiles with logos and information
- **Category System** - Hierarchical categorization
- **User Management** - Separate admin and user dashboards

### 🎪 Coupon Popup Features
- **Interactive Popup** - Animated SweetAlert2 modals
- **Circular Share Icons** - Beautiful social sharing buttons
- **Code Revelation** - Click to reveal coupon codes
- **Copy to Clipboard** - One-click code copying
- **Social Sharing** - Facebook, Twitter, WhatsApp, Telegram
- **Affiliate Tracking** - Complete click and conversion tracking

### 🔗 Affiliate Network Integration
- **vCommission** - Full API integration
- **CueLinks** - Link monetization
- **OptimiseMedia** - Performance marketing
- **INR Deals** - Indian market focus
- **Amazon Associates** - Product API integration
- **Flipkart Affiliate** - E-commerce integration

### 📊 Admin Dashboard
- **Analytics Dashboard** - Complete statistics and charts
- **Header/Footer Editor** - Visual content management
- **Slider Management** - Homepage slider controls
- **Theme Customization** - Colors, fonts, and styling
- **Menu Customization** - Dynamic navigation menus
- **API Key Management** - Secure credential storage
- **SEO Management** - Meta tags and optimization

### 🔔 Advanced Features
- **OneSignal Integration** - Push notifications
- **Favorite Stores** - User preference system
- **PWA Support** - Progressive Web App capabilities
- **Google Site Kit** - SEO and analytics
- **AdSense Integration** - Revenue optimization
- **Page Speed Optimization** - Performance focused

### 📱 User Experience
- **Mega Menu** - Comprehensive navigation
- **Hover Effects** - Interactive UI elements
- **Responsive Design** - Mobile-first approach
- **Search Functionality** - Advanced filtering
- **Pagination** - Efficient content browsing

## 🛠️ Technology Stack

### Backend
- **Laravel 10.0** - PHP Framework
- **PHP 8.1+** - Server-side language
- **SQLite/MySQL** - Database options
- **Spatie Packages** - Permissions, Media, Slugs
- **Guzzle HTTP** - API integrations

### Frontend
- **Bootstrap 5.3** - CSS Framework
- **Font Awesome 6.4** - Icons
- **jQuery 3.7** - JavaScript library
- **SweetAlert2** - Beautiful alerts
- **Animate.css** - CSS animations
- **AOS** - Animate on scroll
- **Chart.js** - Data visualization

### Additional Libraries
- **Swiper** - Touch sliders
- **LazySizes** - Image lazy loading
- **Particles.js** - Background effects
- **Typed.js** - Typing animations
- **CountUp.js** - Number animations
- **DataTables** - Advanced tables
- **Select2** - Enhanced select boxes
- **Flatpickr** - Date picker

## 📋 Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js & npm
- Web server (Apache/Nginx)

### Step 1: Clone Repository
```bash
git clone https://github.com/your-repo/coupon-deals-cms.git
cd coupon-deals-cms
```

### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 3: Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### Step 4: Database Setup
```bash
# Create SQLite database file
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

### Step 5: Build Assets
```bash
# Compile frontend assets
npm run build

# Or for development
npm run dev
```

### Step 6: Storage Setup
```bash
# Create symbolic link for storage
php artisan storage:link

# Set permissions
chmod -R 775 storage bootstrap/cache
```

## 🚀 Quick Start

### Default Admin Login
- **Email**: admin@example.com
- **Password**: password

### Access Points
- **Public Site**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin
- **User Dashboard**: http://localhost:8000/dashboard

### Development Server
```bash
# Start Laravel development server
php artisan serve

# Start Vite development server (separate terminal)
npm run dev
```

## 📁 Project Structure

```
coupon-deals-cms/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # Admin controllers
│   │   ├── Auth/           # Authentication
│   │   └── Public/         # Public controllers
│   ├── Models/             # Eloquent models
│   ├── Services/           # Business logic
│   └── Helpers/            # Helper functions
├── database/
│   ├── migrations/         # Database migrations
│   ├── seeders/           # Database seeders
│   └── factories/         # Model factories
├── resources/
│   ├── views/
│   │   ├── admin/         # Admin templates
│   │   ├── public/        # Public templates
│   │   └── layouts/       # Layout templates
│   ├── js/                # JavaScript files
│   └── css/               # Stylesheets
├── public/
│   ├── images/            # Static images
│   ├── css/               # Compiled CSS
│   ├── js/                # Compiled JS
│   ├── manifest.json      # PWA manifest
│   └── sw.js              # Service worker
└── routes/
    ├── web.php            # Web routes
    ├── api.php            # API routes
    └── auth.php           # Authentication routes
```

## ⚙️ Configuration

### Affiliate Networks Setup

1. **Access Admin Panel** → Affiliate Networks
2. **Add Network** with required credentials:
   - **vCommission**: API Key, Publisher ID
   - **CueLinks**: API Key, Publisher ID
   - **Amazon**: Access Key, Secret Key, Tracking ID
   - **Flipkart**: API Key, Tracking ID

### OneSignal Setup
```env
ONESIGNAL_APP_ID=your_app_id
ONESIGNAL_API_KEY=your_api_key
```

### Google Services
```env
GOOGLE_SITE_KIT_ID=your_site_kit_id
GOOGLE_ANALYTICS_ID=your_analytics_id
GOOGLE_ADSENSE_ID=your_adsense_id
```

## 🎨 Customization

### Theme Customization
- Navigate to **Admin** → **Appearance** → **Theme**
- Customize colors, fonts, and layouts
- Preview changes in real-time

### Menu Management
- **Admin** → **Appearance** → **Menus**
- Drag and drop menu builder
- Support for mega menus

### Header/Footer Editor
- **Admin** → **Appearance** → **Editor**
- Visual WYSIWYG editor
- Custom HTML/CSS support

## 📈 SEO Features

### Built-in SEO
- **Meta Tags** - Title, description, keywords
- **Open Graph** - Social media optimization
- **Twitter Cards** - Twitter sharing
- **Schema Markup** - Structured data
- **XML Sitemaps** - Search engine indexing

### Google Integration
- **Google Site Kit** - Comprehensive analytics
- **Search Console** - Search performance
- **Analytics** - User behavior tracking
- **AdSense** - Revenue optimization

## 🔧 API Documentation

### Tracking APIs
```javascript
// Track coupon clicks
POST /api/track-coupon-click
{
    "coupon_code": "SAVE20",
    "user_agent": "...",
    "referrer": "..."
}

// Track affiliate clicks
POST /api/track-affiliate-click
{
    "affiliate_url": "https://...",
    "user_agent": "...",
    "referrer": "..."
}

// Toggle favorites
POST /api/favorites/toggle
{
    "type": "coupon|deal|product|store",
    "id": 123
}
```

## 🛡️ Security Features

### Authentication
- **Laravel Breeze** - Authentication scaffolding
- **Email Verification** - Account security
- **Password Reset** - Secure recovery
- **Role-based Access** - Spatie Permissions

### Data Protection
- **CSRF Protection** - Cross-site request forgery
- **SQL Injection Protection** - Eloquent ORM
- **XSS Protection** - Output escaping
- **Rate Limiting** - API protection

## 📊 Performance

### Optimization Features
- **Image Lazy Loading** - Faster page loads
- **Asset Minification** - Reduced file sizes
- **Caching** - Redis/Memcached support
- **CDN Ready** - Static asset delivery
- **Database Indexing** - Query optimization

### Monitoring
- **Query Debugging** - Laravel Debugbar
- **Performance Metrics** - Built-in analytics
- **Error Logging** - Comprehensive logging
- **Health Checks** - System monitoring

## 🧪 Testing

### Run Tests
```bash
# Run PHP tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Generate coverage report
php artisan test --coverage
```

## 🚀 Deployment

### Production Setup
```bash
# Optimize for production
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build production assets
npm run build
```

### Environment Variables
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **Laravel Team** - Amazing PHP framework
- **Bootstrap Team** - Responsive CSS framework
- **Spatie** - Excellent Laravel packages
- **Font Awesome** - Beautiful icons
- **All Contributors** - Community support

## 📞 Support

- **Documentation**: [Wiki](https://github.com/your-repo/coupon-deals-cms/wiki)
- **Issues**: [GitHub Issues](https://github.com/your-repo/coupon-deals-cms/issues)
- **Discussions**: [GitHub Discussions](https://github.com/your-repo/coupon-deals-cms/discussions)
- **Email**: support@yourdomain.com

---

<div align="center">
    <p>Made with ❤️ for better deals and savings</p>
    <p>
        <a href="https://github.com/your-repo/coupon-deals-cms">⭐ Star this repo</a> •
        <a href="https://github.com/your-repo/coupon-deals-cms/issues">🐛 Report Bug</a> •
        <a href="https://github.com/your-repo/coupon-deals-cms/issues">💡 Request Feature</a>
    </p>
</div>