#!/bin/bash
# Download Script for Laravel Coupon Deals CMS
# Run this script to download the zip file

echo "🚀 Laravel Coupon Deals CMS - Download Script"
echo "=============================================="
echo ""

# Check if zip file exists
if [ -f "/workspace/coupon-deals-cms.zip" ]; then
    echo "✅ Zip file found at: /workspace/coupon-deals-cms.zip"
    echo "📏 File size: $(ls -lh /workspace/coupon-deals-cms.zip | awk '{print $5}')"
    echo ""
    echo "📋 Download Instructions:"
    echo "1. If you're using a cloud IDE (like Gitpod, CodeSandbox, etc.):"
    echo "   - Look for a download option in your file explorer"
    echo "   - Right-click on coupon-deals-cms.zip and select 'Download'"
    echo ""
    echo "2. If you're using SSH/terminal:"
    echo "   - Use scp to download: scp user@server:/workspace/coupon-deals-cms.zip ."
    echo "   - Or use rsync: rsync -avz user@server:/workspace/coupon-deals-cms.zip ."
    echo ""
    echo "3. If you're using a web server:"
    echo "   - Copy the file to a web-accessible directory"
    echo "   - Access via: http://yourdomain.com/coupon-deals-cms.zip"
    echo ""
    echo "4. Alternative: Recreate the zip file locally"
    echo "   - Copy all files from /workspace/coupon-deals-cms/"
    echo "   - Create zip: zip -r coupon-deals-cms.zip coupon-deals-cms/"
    echo ""
else
    echo "❌ Zip file not found!"
    echo "Creating zip file now..."
    cd /workspace
    zip -r coupon-deals-cms.zip coupon-deals-cms/ -x "coupon-deals-cms/node_modules/*" "coupon-deals-cms/vendor/*" "coupon-deals-cms/.git/*"
    echo "✅ Zip file created successfully!"
fi

echo ""
echo "🎯 Installation Options:"
echo "1. Command Line: php Install.php"
echo "2. Web Interface: http://yourdomain.com/install.php"
echo "3. Manual: composer install && npm install && php artisan migrate"
echo ""
echo "📞 Need help? Check the README.md file for detailed instructions."