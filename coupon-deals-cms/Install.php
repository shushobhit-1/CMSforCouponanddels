<?php
/**
 * Laravel Coupon Deals CMS - Installation Script
 * 
 * This script automates the installation process for the Laravel Coupon Deals CMS.
 * It handles database setup, environment configuration, and dependency installation.
 * 
 * Usage: php Install.php
 */

class LaravelCouponDealsInstaller
{
    private $errors = [];
    private $warnings = [];
    private $success = [];
    private $config = [];
    
    public function __construct()
    {
        $this->config = [
            'app_name' => 'Coupon Deals CMS',
            'app_url' => $this->detectAppUrl(),
            'admin_email' => 'admin@example.com',
            'admin_password' => 'password123',
            'admin_prefix' => 'admi',
            'database_type' => 'sqlite', // sqlite, mysql, pgsql
        ];
    }
    
    public function run()
    {
        echo "\n";
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                Laravel Coupon Deals CMS                     ║\n";
        echo "║                     Installation Script                     ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        
        $this->checkRequirements();
        $this->configureEnvironment();
        $this->installDependencies();
        $this->setupDatabase();
        $this->generateApplicationKey();
        $this->runMigrations();
        $this->createAdminUser();
        $this->buildAssets();
        $this->setPermissions();
        $this->finalizeInstallation();
        
        $this->displayResults();
    }
    
    private function checkRequirements()
    {
        echo "🔍 Checking system requirements...\n";
        
        // Check PHP version
        if (version_compare(PHP_VERSION, '8.1.0', '<')) {
            $this->errors[] = "PHP 8.1 or higher is required. Current version: " . PHP_VERSION;
        } else {
            $this->success[] = "PHP version: " . PHP_VERSION;
        }
        
        // Check required PHP extensions
        $required_extensions = [
            'bcmath', 'ctype', 'fileinfo', 'json', 'mbstring', 
            'openssl', 'pdo', 'tokenizer', 'xml', 'curl', 'gd'
        ];
        
        foreach ($required_extensions as $ext) {
            if (!extension_loaded($ext)) {
                $this->errors[] = "PHP extension '{$ext}' is required but not installed.";
            } else {
                $this->success[] = "PHP extension '{$ext}' is available.";
            }
        }
        
        // Check if Composer is available
        if (!$this->commandExists('composer')) {
            $this->warnings[] = "Composer is not found in PATH. Please install Composer first.";
        } else {
            $this->success[] = "Composer is available.";
        }
        
        // Check if Node.js is available
        if (!$this->commandExists('npm')) {
            $this->warnings[] = "Node.js/npm is not found in PATH. Frontend assets may not build properly.";
        } else {
            $this->success[] = "Node.js/npm is available.";
        }
        
        // Check directory permissions
        $writable_dirs = ['storage', 'bootstrap/cache'];
        foreach ($writable_dirs as $dir) {
            if (!is_writable($dir)) {
                $this->errors[] = "Directory '{$dir}' is not writable.";
            } else {
                $this->success[] = "Directory '{$dir}' is writable.";
            }
        }
        
        echo "✅ Requirements check completed.\n\n";
    }
    
    private function configureEnvironment()
    {
        echo "⚙️  Configuring environment...\n";
        
        if (!file_exists('.env')) {
            if (file_exists('.env.example')) {
                copy('.env.example', '.env');
                $this->success[] = "Created .env file from .env.example";
            } else {
                $this->errors[] = ".env.example file not found.";
                return;
            }
        }
        
        // Update .env file with configuration
        $env_content = file_get_contents('.env');
        
        // Update app settings
        $env_content = $this->updateEnvValue($env_content, 'APP_NAME', '"' . $this->config['app_name'] . '"');
        $env_content = $this->updateEnvValue($env_content, 'APP_URL', $this->config['app_url']);
        $env_content = $this->updateEnvValue($env_content, 'APP_ENV', 'production');
        $env_content = $this->updateEnvValue($env_content, 'APP_DEBUG', 'false');
        
        // Update database settings
        if ($this->config['database_type'] === 'sqlite') {
            $env_content = $this->updateEnvValue($env_content, 'DB_CONNECTION', 'sqlite');
            $env_content = $this->updateEnvValue($env_content, 'DB_DATABASE', database_path('database.sqlite'));
        }
        
        // Update admin prefix
        $env_content = $this->updateEnvValue($env_content, 'ADMIN_PREFIX', $this->config['admin_prefix']);
        
        file_put_contents('.env', $env_content);
        $this->success[] = "Environment configuration updated.";
        
        echo "✅ Environment configuration completed.\n\n";
    }
    
    private function installDependencies()
    {
        echo "📦 Installing dependencies...\n";
        
        // Install Composer dependencies
        if ($this->commandExists('composer')) {
            $output = $this->runCommand('composer install --no-dev --optimize-autoloader');
            if ($output['success']) {
                $this->success[] = "Composer dependencies installed successfully.";
            } else {
                $this->errors[] = "Failed to install Composer dependencies: " . $output['error'];
            }
        } else {
            $this->warnings[] = "Composer not found. Please install dependencies manually.";
        }
        
        // Install NPM dependencies
        if ($this->commandExists('npm')) {
            $output = $this->runCommand('npm install');
            if ($output['success']) {
                $this->success[] = "NPM dependencies installed successfully.";
            } else {
                $this->warnings[] = "Failed to install NPM dependencies: " . $output['error'];
            }
        } else {
            $this->warnings[] = "NPM not found. Please install dependencies manually.";
        }
        
        echo "✅ Dependencies installation completed.\n\n";
    }
    
    private function setupDatabase()
    {
        echo "🗄️  Setting up database...\n";
        
        if ($this->config['database_type'] === 'sqlite') {
            $db_path = database_path('database.sqlite');
            
            // Create SQLite database file
            if (!file_exists($db_path)) {
                touch($db_path);
                chmod($db_path, 0666);
                $this->success[] = "SQLite database file created.";
            } else {
                $this->success[] = "SQLite database file already exists.";
            }
        }
        
        echo "✅ Database setup completed.\n\n";
    }
    
    private function generateApplicationKey()
    {
        echo "🔑 Generating application key...\n";
        
        $output = $this->runCommand('php artisan key:generate --force');
        if ($output['success']) {
            $this->success[] = "Application key generated successfully.";
        } else {
            $this->errors[] = "Failed to generate application key: " . $output['error'];
        }
        
        echo "✅ Application key generation completed.\n\n";
    }
    
    private function runMigrations()
    {
        echo "🔄 Running database migrations...\n";
        
        $output = $this->runCommand('php artisan migrate --force');
        if ($output['success']) {
            $this->success[] = "Database migrations completed successfully.";
        } else {
            $this->errors[] = "Failed to run migrations: " . $output['error'];
        }
        
        echo "✅ Database migrations completed.\n\n";
    }
    
    private function createAdminUser()
    {
        echo "👤 Creating admin user...\n";
        
        // Check if admin user already exists
        $output = $this->runCommand('php artisan tinker --execute="echo App\Models\User::where(\'email\', \'' . $this->config['admin_email'] . '\')->exists() ? \'exists\' : \'not_exists\';"');
        
        if (strpos($output['output'], 'exists') !== false) {
            $this->success[] = "Admin user already exists.";
        } else {
            // Create admin user
            $user_data = [
                'name' => 'Admin',
                'email' => $this->config['admin_email'],
                'password' => bcrypt($this->config['admin_password']),
                'email_verified_at' => now(),
            ];
            
            $output = $this->runCommand('php artisan tinker --execute="App\Models\User::create(' . json_encode($user_data) . '); echo \'created\';"');
            
            if (strpos($output['output'], 'created') !== false) {
                $this->success[] = "Admin user created successfully.";
            } else {
                $this->errors[] = "Failed to create admin user: " . $output['error'];
            }
        }
        
        echo "✅ Admin user setup completed.\n\n";
    }
    
    private function buildAssets()
    {
        echo "🎨 Building frontend assets...\n";
        
        if ($this->commandExists('npm')) {
            $output = $this->runCommand('npm run build');
            if ($output['success']) {
                $this->success[] = "Frontend assets built successfully.";
            } else {
                $this->warnings[] = "Failed to build frontend assets: " . $output['error'];
            }
        } else {
            $this->warnings[] = "NPM not found. Please build assets manually with 'npm run build'.";
        }
        
        echo "✅ Asset building completed.\n\n";
    }
    
    private function setPermissions()
    {
        echo "🔐 Setting file permissions...\n";
        
        $permissions = [
            'storage' => 0755,
            'bootstrap/cache' => 0755,
            'database/database.sqlite' => 0666,
        ];
        
        foreach ($permissions as $path => $permission) {
            if (file_exists($path)) {
                chmod($path, $permission);
                $this->success[] = "Set permissions for '{$path}' to " . substr(sprintf('%o', $permission), -3);
            }
        }
        
        echo "✅ Permissions set successfully.\n\n";
    }
    
    private function finalizeInstallation()
    {
        echo "🎉 Finalizing installation...\n";
        
        // Clear caches
        $commands = [
            'php artisan config:clear',
            'php artisan cache:clear',
            'php artisan route:clear',
            'php artisan view:clear',
        ];
        
        foreach ($commands as $command) {
            $this->runCommand($command);
        }
        
        $this->success[] = "Application caches cleared.";
        
        echo "✅ Installation finalization completed.\n\n";
    }
    
    private function displayResults()
    {
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                    Installation Results                     ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        
        if (!empty($this->success)) {
            echo "✅ SUCCESS:\n";
            foreach ($this->success as $message) {
                echo "   • {$message}\n";
            }
            echo "\n";
        }
        
        if (!empty($this->warnings)) {
            echo "⚠️  WARNINGS:\n";
            foreach ($this->warnings as $message) {
                echo "   • {$message}\n";
            }
            echo "\n";
        }
        
        if (!empty($this->errors)) {
            echo "❌ ERRORS:\n";
            foreach ($this->errors as $message) {
                echo "   • {$message}\n";
            }
            echo "\n";
        }
        
        if (empty($this->errors)) {
            echo "🎉 Installation completed successfully!\n\n";
            echo "📋 Next Steps:\n";
            echo "   1. Access your site: {$this->config['app_url']}\n";
            echo "   2. Admin panel: {$this->config['app_url']}/{$this->config['admin_prefix']}\n";
            echo "   3. Admin login: {$this->config['admin_email']} / {$this->config['admin_password']}\n";
            echo "   4. Change admin password in the admin panel\n";
            echo "   5. Configure your affiliate networks\n";
            echo "   6. Add your first coupons, deals, and products\n\n";
            
            echo "📚 Documentation:\n";
            echo "   • Check the README.md file for detailed instructions\n";
            echo "   • Visit Laravel documentation for framework details\n\n";
            
            echo "🔧 Configuration:\n";
            echo "   • Edit .env file for database and email settings\n";
            echo "   • Configure your web server (Apache/Nginx)\n";
            echo "   • Set up SSL certificate for production\n\n";
        } else {
            echo "❌ Installation failed. Please fix the errors above and try again.\n\n";
        }
    }
    
    private function detectAppUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host;
    }
    
    private function commandExists($command)
    {
        $output = $this->runCommand("which {$command}");
        return $output['success'] && !empty(trim($output['output']));
    }
    
    private function runCommand($command)
    {
        $output = [];
        $return_var = 0;
        
        exec($command . ' 2>&1', $output, $return_var);
        
        return [
            'success' => $return_var === 0,
            'output' => implode("\n", $output),
            'error' => $return_var !== 0 ? implode("\n", $output) : '',
        ];
    }
    
    private function updateEnvValue($content, $key, $value)
    {
        $pattern = "/^{$key}=.*/m";
        $replacement = "{$key}={$value}";
        
        if (preg_match($pattern, $content)) {
            return preg_replace($pattern, $replacement, $content);
        } else {
            return $content . "\n{$key}={$value}";
        }
    }
}

// Run the installer
if (php_sapi_name() === 'cli') {
    $installer = new LaravelCouponDealsInstaller();
    $installer->run();
} else {
    echo "This script should be run from the command line: php Install.php";
}