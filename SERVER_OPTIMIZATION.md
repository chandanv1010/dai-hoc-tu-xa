# Server Configuration for PageSpeed Optimization

## 1. Enable Gzip/Brotli Compression

### Nginx Configuration
Add to your nginx config file (usually in `/etc/nginx/nginx.conf` or site config):

```nginx
# Gzip Settings
gzip on;
gzip_vary on;
gzip_proxied any;
gzip_comp_level 6;
gzip_types
    text/plain
    text/css
    text/xml
    text/javascript
    application/json
    application/javascript
    application/xml+rss
    application/rss+xml
    font/truetype
    font/opentype
    application/vnd.ms-fontobject
    image/svg+xml;

# Brotli Settings (if available)
brotli on;
brotli_comp_level 6;
brotli_types
    text/plain
    text/css
    text/xml
    text/javascript
    application/json
    application/javascript
    application/xml+rss
    application/rss+xml
    font/truetype
    font/opentype
    application/vnd.ms-fontobject
    image/svg+xml;
```

### Apache Configuration (.htaccess)
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>
```

## 2. Browser Caching Headers

### Nginx Configuration
```nginx
# Cache static assets
location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
    access_log off;
}

# Cache HTML with shorter duration
location ~* \.(html)$ {
    expires 1h;
    add_header Cache-Control "public, must-revalidate";
}
```

### Laravel - Add to public/.htaccess
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    
    # Images
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType image/x-icon "access plus 1 year"
    
    # CSS and JavaScript
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType text/javascript "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    
    # Fonts
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType application/vnd.ms-fontobject "access plus 1 year"
    ExpiresByType font/ttf "access plus 1 year"
    ExpiresByType font/otf "access plus 1 year"
</IfModule>

<IfModule mod_headers.c>
    <FilesMatch "\.(jpg|jpeg|png|gif|svg|ico|webp)$">
        Header set Cache-Control "max-age=31536000, public, immutable"
    </FilesMatch>
    <FilesMatch "\.(css|js)$">
        Header set Cache-Control "max-age=31536000, public, immutable"
    </FilesMatch>
    <FilesMatch "\.(woff|woff2|eot|ttf|otf)$">
        Header set Cache-Control "max-age=31536000, public, immutable"
    </FilesMatch>
</IfModule>
```

## 3. Minify Assets

### Run these commands in your Laravel project:

```bash
# Install dependencies if not already installed
npm install

# Build for production (this will minify CSS/JS)
npm run build

# Or if using Vite
npm run build
```

## 4. Optimize Images

### Install image optimization tools:
```bash
# For WebP conversion
npm install --save-dev imagemin imagemin-webp

# Or use online tools:
# - https://squoosh.app/
# - https://tinypng.com/
```

### Batch convert images to WebP:
Create a script `optimize-images.js`:
```javascript
const imagemin = require('imagemin');
const imageminWebp = require('imagemin-webp');

(async () => {
    await imagemin(['public/frontend/resources/img/*.{jpg,png}'], {
        destination: 'public/frontend/resources/img/webp',
        plugins: [
            imageminWebp({quality: 80})
        ]
    });
    console.log('Images optimized!');
})();
```

Run: `node optimize-images.js`

## 5. Enable HTTP/2

### Nginx
```nginx
listen 443 ssl http2;
listen [::]:443 ssl http2;
```

### Apache
Enable mod_http2:
```bash
sudo a2enmod http2
sudo systemctl restart apache2
```

Add to config:
```apache
Protocols h2 http/1.1
```

## 6. Verify Changes

After making changes, verify with:
- PageSpeed Insights: https://pagespeed.web.dev/
- GTmetrix: https://gtmetrix.com/
- WebPageTest: https://www.webpagetest.org/

Check specific optimizations:
- Compression: https://www.giftofspeed.com/gzip-test/
- Caching: Check response headers in browser DevTools
