# Coupon Deals and Affiliate Product CMS

A modern, comprehensive Laravel-based CMS for managing coupons, deals, affiliate products, and stores with a beautiful Bootstrap UI and advanced features.

## 🚀 Features

### Core CMS Features
- **Modern Admin Dashboard** - Beautiful Bootstrap 5.3.0 UI with responsive design
- **User Management** - Separate admin and user dashboards with role-based access control
- **Content Management** - Manage coupons, deals, products, stores, and categories
- **Media Management** - Advanced image handling with Spatie Media Library
- **SEO Optimization** - Full SEO support with meta tags, Open Graph, and Twitter Cards

### Coupon & Deal Management
- **Coupon Popup System** - Customizable popup with share icons and code revelation
- **Deal Management** - "Get Deal" buttons with hover effects
- **Product Management** - "Check Product" buttons with hover effects
- **Store Integration** - Complete store management with logos and banners
- **Category Organization** - Hierarchical category system with mega menu support

### Affiliate Network Integration
- **Multiple Networks** - vCommission, Cuelinks, OptimiseMedia, INR Deals, Amazon, Flipkart
- **API Key Management** - Secure credential storage with encryption
- **Tracking & Analytics** - Click and conversion tracking from admin dashboard
- **Commission Management** - Track revenue and commission rates

### Advanced Features
- **OneSignal Integration** - Push notifications for new coupons/deals
- **PWA Support** - Progressive Web App capabilities
- **Theme Customization** - Admin-controlled theme and typography settings
- **Header/Footer Editor** - Customize site appearance from admin panel
- **Slider Management** - Dynamic slider creation and management
- **Menu Customization** - Flexible menu system with drag-and-drop support

### Performance & UX
- **Page Speed Optimization** - Optimized assets and lazy loading
- **Hover Effects** - Smooth animations and hover effects on all buttons
- **Responsive Design** - Mobile-first approach with Bootstrap 5
- **Font Awesome Icons** - Beautiful iconography throughout the system

## 🛠️ Technology Stack

### Backend
- **Laravel 10.0** - Modern PHP framework
- **PHP 8.1+** - Latest PHP features and performance
- **MySQL/PostgreSQL** - Database support
- **Redis** - Caching and session storage

### Frontend
- **Bootstrap 5.3.0** - Modern CSS framework
- **Font Awesome 6.4.0** - Icon library
- **jQuery 3.7.0** - JavaScript library
- **Vite** - Modern build tool

### Packages & Libraries
- **Spatie Laravel Permission** - Role and permission management
- **Spatie Laravel Media Library** - Advanced media handling
- **Spatie Laravel Backup** - Automated backups
- **Spatie Laravel Activity Log** - User activity tracking
- **Chart.js** - Data visualization
- **SweetAlert2** - Beautiful alerts and confirmations
- **DataTables** - Advanced table functionality

## 📋 Requirements

- PHP 8.1 or higher
- Composer 2.0 or higher
- Node.js 16.0 or higher
- MySQL 8.0 or PostgreSQL 12 or higher
- Redis (optional, for caching)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
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
DB_DATABASE=coupon_cms
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

### 9. Start Development Server
```bash
php artisan serve
```

## 🗄️ Database Structure

### Core Tables
- `users` - User accounts and authentication
- `coupons` - Coupon information and settings
- `deals` - Deal information and settings
- `products` - Product information and affiliate data
- `stores` - Store information and branding
- `categories` - Hierarchical category system
- `affiliates` - Affiliate network configurations

### Relationship Tables
- `favorites` - User favorite items
- `coupon_clicks` - Click tracking for coupons
- `coupon_conversions` - Conversion tracking for coupons
- `store_clicks` - Click tracking for stores
- `store_conversions` - Conversion tracking for stores

## 🔐 Authentication & Authorization

### User Roles
- **Admin** - Full system access
- **User** - Limited access to user dashboard
- **Guest** - Public access only

### Permission System
- Role-based access control
- Granular permissions for different CMS sections
- Admin middleware protection

## 🎨 Admin Dashboard Features

### Dashboard Overview
- Statistics cards with real-time data
- Chart visualizations (monthly, daily, category performance)
- Recent activity feed
- System health monitoring
- Quick action buttons

### Content Management
- **Coupons** - CRUD operations with bulk actions
- **Deals** - Deal management with performance tracking
- **Products** - Product catalog with affiliate integration
- **Stores** - Store management with branding tools
- **Categories** - Category hierarchy management

### User Management
- User registration and management
- Role assignment and permissions
- User activity tracking
- Profile management

### System Settings
- **Theme Customization** - Colors, fonts, and layout options
- **Header/Footer Editor** - Customize site appearance
- **Slider Management** - Dynamic slider creation
- **Menu Management** - Custom menu structures
- **Affiliate Settings** - Network configurations and API keys

## 🌐 Public Features

### Homepage
- Hero section with featured content
- Category showcase
- Recent coupons and deals
- Store highlights

### Category Pages
- Hierarchical category navigation
- Filter and search options
- Hover effects on all elements
- Responsive grid layouts

### Coupon/Deal Pages
- Detailed information display
- Coupon popup with share functionality
- Affiliate link tracking
- User reviews and ratings

### Store Pages
- Store information and branding
- Available coupons and deals
- Store reviews and ratings
- Location and contact information

## 📱 Mobile & PWA

### Progressive Web App
- Offline functionality
- App-like experience
- Push notifications
- Install prompts

### Mobile Optimization
- Responsive design
- Touch-friendly interfaces
- Optimized performance
- Mobile-specific features

## 🔗 Affiliate Integration

### Supported Networks
- **vCommission** - API integration with tracking
- **Cuelinks** - Publisher management
- **OptimiseMedia** - Performance tracking
- **INR Deals** - Indian market focus
- **Amazon** - Product catalog integration
- **Flipkart** - Indian e-commerce integration

### Features
- Secure API key storage
- Automatic data synchronization
- Performance analytics
- Commission tracking
- Network health monitoring

## 📊 Analytics & Reporting

### Dashboard Analytics
- Click and conversion tracking
- Revenue analytics
- Performance metrics
- User behavior insights

### Export Features
- CSV export for all data
- Custom date range filtering
- Performance reports
- Affiliate network reports

## 🎯 SEO Features

### On-Page SEO
- Meta title and description management
- Open Graph and Twitter Card support
- Structured data markup
- Canonical URL management

### Technical SEO
- Clean URL structure
- XML sitemap generation
- Robots.txt optimization
- Page speed optimization

## 🚀 Performance Optimization

### Frontend Optimization
- Asset minification and bundling
- Lazy loading for images
- CSS and JavaScript optimization
- CDN support

### Backend Optimization
- Database query optimization
- Caching strategies
- Image optimization
- API response optimization

## 🔒 Security Features

### Authentication Security
- Laravel Sanctum for API authentication
- Password hashing and validation
- Two-factor authentication support
- Session security

### Data Protection
- CSRF protection
- SQL injection prevention
- XSS protection
- Input validation and sanitization

## 🧪 Testing

### Test Coverage
- Unit tests for models
- Feature tests for controllers
- Browser tests for user flows
- API tests for endpoints

### Running Tests
```bash
php artisan test
php artisan test --coverage
```

## 📦 Deployment

### Production Setup
1. Set production environment variables
2. Optimize Composer autoloader
3. Build production assets
4. Configure web server
5. Set up SSL certificates
6. Configure caching and queues

### Server Requirements
- PHP 8.1+
- MySQL 8.0+ or PostgreSQL 12+
- Redis (recommended)
- Nginx or Apache
- SSL certificate

## 🤝 Contributing

### Development Setup
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

### Coding Standards
- Follow PSR-12 coding standards
- Use Laravel Pint for code formatting
- Write meaningful commit messages
- Include tests for new features

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

### Documentation
- [User Guide](docs/user-guide.md)
- [Admin Guide](docs/admin-guide.md)
- [API Documentation](docs/api.md)
- [Developer Guide](docs/developer-guide.md)

### Getting Help
- Create an issue on GitHub
- Check the documentation
- Review existing issues
- Contact the development team

## 🔄 Changelog

### Version 1.0.0
- Initial release
- Core CMS functionality
- Admin dashboard
- User management
- Coupon and deal management
- Affiliate integration
- Modern Bootstrap UI

## 🎉 Acknowledgments

- Laravel team for the amazing framework
- Bootstrap team for the UI framework
- Spatie for excellent Laravel packages
- All contributors and supporters

---

**Built with ❤️ using Laravel and Bootstrap**