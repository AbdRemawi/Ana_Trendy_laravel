# Production Upload Size Fix - 413 Request Entity Too Large

## Problem
The 413 error occurs when the web server rejects large uploads BEFORE they reach PHP/Laravel.

## Quick Diagnosis
Check which web server you're using on production:

```bash
# SSH into your server and run:
nginx -v 2>/dev/null && echo "Using nginx" || apache2 -v 2>/dev/null && echo "Using Apache"
```

---

## Solution 1: Nginx (Most Common)

### Step 1: Find your nginx config location
```bash
# Common locations:
/etc/nginx/nginx.conf              # Main config
/etc/nginx/sites-available/your-site  # Site-specific config
/etc/nginx/conf.d/upload.conf      # Custom config
```

### Step 2: Add or modify this directive
```nginx
http {
    # ... other settings
    client_max_body_size 50M;
}

# OR in your server block:
server {
    # ... other settings
    client_max_body_size 50M;

    location / {
        # ... other settings
        client_max_body_size 50M;
    }
}
```

### Step 3: Restart nginx
```bash
sudo nginx -t                    # Test config
sudo systemctl restart nginx     # Restart nginx
# OR
sudo service nginx restart
```

---

## Solution 2: Apache

### Option A: Using .htaccess (Already Done)
The `.htaccess` file in the `public/` directory now includes:
```apache
LimitRequestBody 52428800
```

### Option B: Apache Config (If .htaccess is Not Working)
Edit your Apache virtual host file:
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/your-app/public

    # Increase upload limit
    LimitRequestBody 52428800

    # Other settings...
</VirtualHost>
```

Then restart Apache:
```bash
sudo systemctl restart apache2   # Ubuntu/Debian
# OR
sudo service httpd restart       # CentOS/RHEL
```

---

## Solution 3: Cloudflare / Load Balancers

If you're using Cloudflare, add a page rule:

1. Go to Cloudflare Dashboard
2. Rules > Page Rules
3. Create a new rule: `*yourdomain.com/*`
4. Setting: `Upload File Size Limit` = `50 MB`

---

## Solution 4: Shared Hosting (cPanel, etc.)

### Option A: cPanel
1. Login to cPanel
2. Select "Select PHP Version"
3. Go to "Options" tab
4. Set:
   - `upload_max_filesize` = 50M
   - `post_max_size` = 50M

### Option B: Contact Hosting Support
Request them to increase:
- `upload_max_filesize` to 50M
- `post_max_size` to 50M
- `client_max_body_size` (if nginx) to 50M
- `LimitRequestBody` (if Apache) to 52428800

---

## Verification

After making changes, test with:

```bash
# Create a test PHP file (remove after testing)
echo "<?php phpinfo(); ?>" > public/info.php

# Visit: https://yourdomain.com/info.php
# Check these values:
# - upload_max_filesize = 50M
# - post_max_size = 50M

# Remove the test file
rm public/info.php
```

---

## Common Issues

### Issue: .htaccess changes not taking effect
**Solution:** Check Apache allows overrides:
```apache
<Directory "/var/www/your-app/public">
    AllowOverride All
</Directory>
```

### Issue: Still getting 413 after nginx changes
**Solution:** Check for additional nginx config files:
```bash
grep -r "client_max_body_size" /etc/nginx/
```

### Issue: Works in some locations but not others
**Solution:** You might have multiple nginx/Apache instances. Check all configs.

---

## Deploying the Updated .htaccess

The `.htaccess` file has been updated. Deploy it:

```bash
# If using git:
git add public/.htaccess
git commit -m "Add Apache LimitRequestBody for large uploads"
git push

# Then deploy on your server:
git pull
```

---

## Quick Checklist

- [ ] Updated nginx `client_max_body_size`
- [ ] Updated Apache `LimitRequestBody` (in .htaccess)
- [ ] Restarted web server
- [ ] Verified with phpinfo()
- [ ] Tested upload with large file
- [ ] Checked Cloudflare/proxy settings (if applicable)

---

## Still Not Working?

Run this diagnostic command on your server:

```bash
# Nginx
nginx -V 2>&1 | grep -o --conf-path.* && cat $(nginx -V 2>&1 | grep -o 'conf-path=[^ ]*' | cut -d= -f2) | grep client_max_body_size

# Apache
apache2ctl -V | grep SERVER_CONFIG_FILE && cat /etc/apache2/apache2.conf | grep LimitRequestBody

# PHP
php -i | grep -E "upload_max_filesize|post_max_size"
```

Share the output and I can help you further!
