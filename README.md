# Coupon Deals & Affiliate Product CMS

A modern, comprehensive Laravel-based CMS for managing coupons, deals, and affiliate products with a beautiful Bootstrap UI and advanced features.

## 🚀 Features

### Core CMS Features
- **Modern Admin Dashboard** - Bootstrap 5.3.0 based admin panel with comprehensive statistics
- **Content Management** - Manage coupons, deals, products, stores, and categories
- **User Management** - Separate admin and user dashboards with role-based permissions
- **Media Management** - Spatie Media Library integration for image handling
- **SEO Optimization** - Meta tags, Open Graph, Twitter Cards, and SEO scoring

### Coupon & Deal Features
- **Coupon Popup System** - Interactive popup with share icons and code revelation
- **Smart Tracking** - Track clicks, conversions, and user engagement
- **Expiration Management** - Automatic expiration handling and notifications
- **Store Integration** - Link coupons and deals to stores with commission tracking

### Affiliate Network Integration
- **Multiple Networks** - vCommission, Cuelinks, OptimiseMedia, INR Deals, Amazon, Flipkart
- **API Management** - Secure API key storage and management from admin dashboard
- **Auto-sync** - Automated data synchronization with affiliate networks
- **Performance Tracking** - Monitor clicks, conversions, and revenue

### User Experience Features
- **Mega Menu** - Dynamic navigation with category-based organization
- **Advanced Search** - Multi-criteria search with filters and sorting
- **Responsive Design** - Mobile-first approach with Bootstrap 5
- **Hover Effects** - Smooth animations and interactive elements
- **PWA Support** - Progressive Web App capabilities

### Technical Features
- **Laravel 10** - Latest Laravel framework with modern PHP 8.1+ features
- **Performance** - Optimized for speed with caching and lazy loading
- **Security** - CSRF protection, authentication, and role-based access control
- **Notifications** - OneSignal integration for push notifications
- **Analytics** - Built-in analytics and reporting system

## 🛠️ Technology Stack

### Backend
- **PHP 8.1+** - Modern PHP with type hints and attributes
- **Laravel 10** - Latest Laravel framework
- **MySQL/PostgreSQL** - Database support
- **Redis** - Caching and session storage

### Frontend
- **Bootstrap 5.3.0** - Modern CSS framework
- **Font Awesome 6.4.0** - Icon library
- **jQuery 3.7.0** - JavaScript library
- **Vite** - Asset bundling and compilation

### Additional Libraries
- **SweetAlert2** - Beautiful alerts and modals
- **Animate.css** - CSS animations
- **AOS** - Animate On Scroll library
- **Swiper** - Touch slider
- **Chart.js** - Data visualization
- **DataTables** - Enhanced tables
- **Select2** - Enhanced select boxes
- **Flatpickr** - Date picker

## 📋 Requirements

- PHP 8.1 or higher
- Composer 2.0 or higher
- Node.js 16.0 or higher
- MySQL 8.0 or PostgreSQL 13 or higher
- Redis (optional, for caching)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/coupon-deals-cms.git
cd coupon-deals-cms
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node.js Dependencies
```bash
npm install
```

### 4. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure Database
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=coupon_deals_cms
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run Migrations
```bash
php artisan migrate
```

### 7. Seed Database
```bash
php artisan db:seed
```

### 8. Build Assets
```bash
npm run build
```

### 9. Set Permissions
```bash
chmod -R 775 storage bootstrap/cache
```

### 10. Start Development Server
```bash
php artisan serve
```

## 🔧 Configuration

### Affiliate Networks
Configure affiliate network API keys in the admin dashboard:
1. Go to Admin → Affiliate Networks
2. Add new network with API credentials
3. Configure sync settings and commission rates

### OneSignal Setup
1. Create OneSignal account
2. Get App ID
3. Add to `.env`:
```env
ONESIGNAL_APP_ID=your_app_id
ONESIGNAL_REST_API_KEY=your_api_key
```

### Email Configuration
Configure SMTP settings in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

## 📱 Usage

### Admin Dashboard
- **Dashboard**: Overview of statistics and recent activity
- **Content Management**: Manage coupons, deals, products, stores, categories
- **User Management**: Manage users, roles, and permissions
- **Settings**: Configure site settings, themes, and integrations
- **Analytics**: View performance metrics and reports

### Public Frontend
- **Homepage**: Featured content and categories
- **Search**: Advanced search with filters
- **Categories**: Browse content by category
- **Stores**: View store information and offers
- **User Dashboard**: Personal favorites and notifications

### Coupon System
1. **Browse Coupons**: View available coupons with filters
2. **Click Coupon**: Click to reveal coupon code
3. **Share**: Share coupons on social media
4. **Track**: Monitor usage and performance

## 🎨 Customization

### Themes
- Modify `public/css/public.css` for frontend styling
- Edit `public/css/admin.css` for admin panel styling
- Use CSS variables for consistent theming

### Layouts
- Frontend: `resources/views/public/layouts/app.blade.php`
- Admin: `resources/views/admin/layouts/app.blade.php`

### Components
- Create reusable Blade components in `resources/views/components/`
- Add custom JavaScript in `public/js/public.js`

## 🔒 Security Features

- **CSRF Protection** - Built-in Laravel CSRF protection
- **Authentication** - Secure user authentication system
- **Role-based Access** - Spatie Laravel Permission integration
- **Input Validation** - Comprehensive form validation
- **SQL Injection Protection** - Eloquent ORM protection
- **XSS Protection** - Blade template escaping

## 📊 Performance Optimization

- **Caching** - Redis and file-based caching
- **Lazy Loading** - Image and content lazy loading
- **Asset Optimization** - Vite bundling and minification
- **Database Optimization** - Efficient queries and indexing
- **CDN Ready** - Static asset delivery optimization

## 🧪 Testing

### Run Tests
```bash
php artisan test
```

### Run Specific Test Suite
```bash
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

## 📈 Monitoring & Analytics

- **Built-in Analytics** - Track user behavior and performance
- **Error Logging** - Comprehensive error tracking
- **Performance Metrics** - Monitor page speed and load times
- **User Engagement** - Track clicks, conversions, and engagement

## 🔄 Deployment

### Production Setup
1. Set `APP_ENV=production` in `.env`
2. Run `php artisan config:cache`
3. Run `php artisan route:cache`
4. Run `php artisan view:cache`
5. Set up queue workers for background jobs

### Server Requirements
- Nginx/Apache web server
- PHP-FPM 8.1+
- MySQL 8.0+ or PostgreSQL 13+
- Redis (recommended)
- SSL certificate

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Documentation**: Check this README and inline code comments
- **Issues**: Report bugs and feature requests via GitHub Issues
- **Discussions**: Join community discussions on GitHub Discussions

## 🗺️ Roadmap

### Phase 2 (Q2 2024)
- [ ] Advanced analytics dashboard
- [ ] Multi-language support
- [ ] API endpoints for mobile apps
- [ ] Advanced reporting system

### Phase 3 (Q3 2024)
- [ ] AI-powered recommendations
- [ ] Advanced automation features
- [ ] Integration with more affiliate networks
- [ ] Mobile app development

## 🙏 Acknowledgments

- **Laravel Team** - For the amazing framework
- **Bootstrap Team** - For the UI framework
- **Spatie** - For excellent Laravel packages
- **Open Source Community** - For all the libraries and tools

---

**Built with ❤️ using Laravel and modern web technologies**

For more information, visit [your-website.com](https://your-website.com)