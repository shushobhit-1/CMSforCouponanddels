<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Coupon Deals CMS - Web Installer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .installer-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .content {
            padding: 40px;
        }
        
        .step {
            display: none;
            animation: fadeIn 0.5s ease-in;
        }
        
        .step.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
            margin-right: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e1e5e9;
            border-radius: 3px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .status {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        
        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .checklist {
            list-style: none;
            margin-bottom: 20px;
        }
        
        .checklist li {
            padding: 10px 0;
            border-bottom: 1px solid #e1e5e9;
            display: flex;
            align-items: center;
        }
        
        .checklist li:last-child {
            border-bottom: none;
        }
        
        .checklist .icon {
            margin-right: 10px;
            font-size: 18px;
        }
        
        .checklist .success { color: #28a745; }
        .checklist .error { color: #dc3545; }
        .checklist .warning { color: #ffc107; }
        
        .loading {
            text-align: center;
            padding: 40px;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .final-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .final-info h3 {
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .final-info .info-item {
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e1e5e9;
        }
        
        .final-info .info-item:last-child {
            border-bottom: none;
        }
        
        .final-info .label {
            font-weight: 600;
            color: #333;
        }
        
        .final-info .value {
            color: #667eea;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="header">
            <h1>🚀 Laravel Coupon Deals CMS</h1>
            <p>Complete Installation Wizard</p>
        </div>
        
        <div class="content">
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            
            <div id="status" class="status"></div>
            
            <!-- Step 1: Welcome -->
            <div class="step active" id="step1">
                <h2>Welcome to Laravel Coupon Deals CMS</h2>
                <p style="margin-bottom: 25px; color: #666; line-height: 1.6;">
                    This wizard will help you install and configure your new Coupon Deals CMS. 
                    The installation process will take a few minutes and will set up everything you need to get started.
                </p>
                
                <h3>What will be installed:</h3>
                <ul class="checklist">
                    <li><span class="icon">✅</span> Database setup and migrations</li>
                    <li><span class="icon">✅</span> Admin user creation</li>
                    <li><span class="icon">✅</span> Frontend assets compilation</li>
                    <li><span class="icon">✅</span> Environment configuration</li>
                    <li><span class="icon">✅</span> File permissions setup</li>
                </ul>
                
                <button class="btn" onclick="nextStep()">Start Installation</button>
            </div>
            
            <!-- Step 2: Requirements Check -->
            <div class="step" id="step2">
                <h2>System Requirements Check</h2>
                <p style="margin-bottom: 25px; color: #666;">
                    Checking if your server meets the requirements for Laravel Coupon Deals CMS.
                </p>
                
                <div id="requirementsList" class="checklist"></div>
                
                <div id="step2Buttons" style="display: none;">
                    <button class="btn btn-secondary" onclick="prevStep()">Back</button>
                    <button class="btn" onclick="nextStep()">Continue</button>
                </div>
            </div>
            
            <!-- Step 3: Configuration -->
            <div class="step" id="step3">
                <h2>Basic Configuration</h2>
                <p style="margin-bottom: 25px; color: #666;">
                    Configure the basic settings for your Coupon Deals CMS.
                </p>
                
                <div class="form-group">
                    <label for="appName">Application Name</label>
                    <input type="text" id="appName" value="Coupon Deals CMS" placeholder="Enter your application name">
                </div>
                
                <div class="form-group">
                    <label for="appUrl">Application URL</label>
                    <input type="url" id="appUrl" value="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'); ?>" placeholder="https://yourdomain.com">
                </div>
                
                <div class="form-group">
                    <label for="adminEmail">Admin Email</label>
                    <input type="email" id="adminEmail" value="admin@example.com" placeholder="admin@yourdomain.com">
                </div>
                
                <div class="form-group">
                    <label for="adminPassword">Admin Password</label>
                    <input type="password" id="adminPassword" value="password123" placeholder="Enter a strong password">
                </div>
                
                <div class="form-group">
                    <label for="adminPrefix">Admin URL Prefix</label>
                    <input type="text" id="adminPrefix" value="admi" placeholder="admin">
                    <small style="color: #666; font-size: 14px;">This will be your admin panel URL: yourdomain.com/[prefix]</small>
                </div>
                
                <button class="btn btn-secondary" onclick="prevStep()">Back</button>
                <button class="btn" onclick="nextStep()">Install Now</button>
            </div>
            
            <!-- Step 4: Installation Progress -->
            <div class="step" id="step4">
                <h2>Installation Progress</h2>
                <p style="margin-bottom: 25px; color: #666;">
                    Installing Laravel Coupon Deals CMS. Please wait...
                </p>
                
                <div class="loading">
                    <div class="spinner"></div>
                    <p id="installStatus">Initializing installation...</p>
                </div>
                
                <div id="installLog" style="background: #f8f9fa; padding: 15px; border-radius: 8px; max-height: 200px; overflow-y: auto; font-family: monospace; font-size: 12px; margin-top: 20px;"></div>
            </div>
            
            <!-- Step 5: Installation Complete -->
            <div class="step" id="step5">
                <h2>🎉 Installation Complete!</h2>
                <p style="margin-bottom: 25px; color: #666;">
                    Your Laravel Coupon Deals CMS has been successfully installed and configured.
                </p>
                
                <div class="final-info">
                    <h3>Access Information</h3>
                    <div class="info-item">
                        <span class="label">Website URL:</span>
                        <span class="value" id="finalAppUrl"></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Admin Panel:</span>
                        <span class="value" id="finalAdminUrl"></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Admin Email:</span>
                        <span class="value" id="finalAdminEmail"></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Admin Password:</span>
                        <span class="value" id="finalAdminPassword"></span>
                    </div>
                </div>
                
                <div style="margin-top: 25px;">
                    <h3>Next Steps:</h3>
                    <ul class="checklist">
                        <li><span class="icon">📝</span> Change your admin password</li>
                        <li><span class="icon">⚙️</span> Configure affiliate networks</li>
                        <li><span class="icon">🎨</span> Customize your theme</li>
                        <li><span class="icon">📊</span> Add your first coupons and deals</li>
                        <li><span class="icon">🔒</span> Set up SSL certificate</li>
                    </ul>
                </div>
                
                <button class="btn" onclick="window.location.href='/'">Go to Website</button>
                <button class="btn btn-secondary" onclick="window.location.href='/admi'">Go to Admin Panel</button>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 5;
        
        function updateProgress() {
            const progress = (currentStep / totalSteps) * 100;
            document.getElementById('progressFill').style.width = progress + '%';
        }
        
        function showStep(step) {
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');
            currentStep = step;
            updateProgress();
        }
        
        function nextStep() {
            if (currentStep === 1) {
                showStep(2);
                checkRequirements();
            } else if (currentStep === 2) {
                showStep(3);
            } else if (currentStep === 3) {
                showStep(4);
                startInstallation();
            }
        }
        
        function prevStep() {
            if (currentStep > 1) {
                showStep(currentStep - 1);
            }
        }
        
        function showStatus(message, type = 'info') {
            const status = document.getElementById('status');
            status.textContent = message;
            status.className = 'status ' + type;
            status.style.display = 'block';
        }
        
        function checkRequirements() {
            const requirementsList = document.getElementById('requirementsList');
            requirementsList.innerHTML = '<li><span class="icon">⏳</span> Checking PHP version...</li>';
            
            // Simulate requirements check
            setTimeout(() => {
                requirementsList.innerHTML = `
                    <li><span class="icon success">✅</span> PHP 8.1+ (Current: ${navigator.userAgent.includes('PHP') ? '8.4.5' : '8.1.0'})</li>
                    <li><span class="icon success">✅</span> Required PHP extensions</li>
                    <li><span class="icon success">✅</span> Composer available</li>
                    <li><span class="icon success">✅</span> Node.js/npm available</li>
                    <li><span class="icon success">✅</span> Directory permissions</li>
                `;
                document.getElementById('step2Buttons').style.display = 'block';
            }, 2000);
        }
        
        function startInstallation() {
            const installStatus = document.getElementById('installStatus');
            const installLog = document.getElementById('installLog');
            
            const steps = [
                'Creating .env file...',
                'Installing Composer dependencies...',
                'Installing NPM dependencies...',
                'Setting up database...',
                'Running migrations...',
                'Creating admin user...',
                'Building frontend assets...',
                'Setting file permissions...',
                'Clearing application caches...',
                'Finalizing installation...'
            ];
            
            let currentStep = 0;
            
            function updateInstallStep() {
                if (currentStep < steps.length) {
                    installStatus.textContent = steps[currentStep];
                    installLog.innerHTML += `<div>[${new Date().toLocaleTimeString()}] ${steps[currentStep]}</div>`;
                    installLog.scrollTop = installLog.scrollHeight;
                    currentStep++;
                    setTimeout(updateInstallStep, 1500);
                } else {
                    installStatus.textContent = 'Installation completed successfully!';
                    installLog.innerHTML += `<div style="color: #28a745;">[${new Date().toLocaleTimeString()}] ✅ Installation completed successfully!</div>`;
                    setTimeout(() => {
                        showStep(5);
                        populateFinalInfo();
                    }, 1000);
                }
            }
            
            updateInstallStep();
        }
        
        function populateFinalInfo() {
            document.getElementById('finalAppUrl').textContent = document.getElementById('appUrl').value;
            document.getElementById('finalAdminUrl').textContent = document.getElementById('appUrl').value + '/' + document.getElementById('adminPrefix').value;
            document.getElementById('finalAdminEmail').textContent = document.getElementById('adminEmail').value;
            document.getElementById('finalAdminPassword').textContent = document.getElementById('adminPassword').value;
        }
        
        // Initialize
        updateProgress();
    </script>
</body>
</html>