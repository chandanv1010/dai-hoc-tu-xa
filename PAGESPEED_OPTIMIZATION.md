# Hướng dẫn Tối ưu PageSpeed Insights

## Các vấn đề đã được tối ưu trong code:

### ✅ 1. LCP (Largest Contentful Paint) Optimization
- **Đã thêm `fetchpriority="high"`** cho slide image đầu tiên (LCP element)
- **Đã thêm `loading="eager"`** cho LCP image
- **Đã preload LCP image** trong `<head>` cho homepage
- **Đã thêm `loading="lazy"`** cho các images không quan trọng

### ✅ 2. Image Optimization
- **Đã thêm `width` và `height`** cho logo để tránh CLS (Cumulative Layout Shift)
- **Đã thêm `loading="lazy"`** cho school logos và major images trên homepage

### ✅ 3. JavaScript Optimization
- **Đã thêm `defer`** cho jQuery, lottie-player, và các scripts khác
- Scripts không còn block rendering

### ✅ 4. Resource Hints
- **Đã thêm `dns-prefetch`** cho unpkg.com, Google Analytics, Google Tag Manager
- **Đã thêm `preconnect`** cho Google Fonts

## Các vấn đề cần cấu hình trên Server (Nginx):

### 🔧 1. Cache Headers cho Static Assets
**Vấn đề:** Images, CSS, JS chỉ có cache 4-12 giờ → Cần tăng lên 1 năm

**Cách sửa trên aapanel:**
1. Vào **Website** → Chọn site → **Cấu hình**
2. Thêm vào block `server`:

```nginx
# Cache static files for 1 year
location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot|webp)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
    access_log off;
}
```

**Lợi ích:** Tiết kiệm 669 KiB cho repeat visitors

### 🔧 2. Gzip/Brotli Compression
**Cách sửa:**
```nginx
# Enable gzip
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json image/svg+xml;
```

**Lợi ích:** Giảm kích thước file CSS/JS/HTML

### 🔧 3. HTTP/2 Server Push (Optional)
Có thể push critical CSS và fonts

## Các vấn đề cần tối ưu thêm (nếu cần):

### 📝 1. Image Format Optimization
**Vấn đề:** Một số images vẫn dùng JPG/PNG thay vì WebP
- Logo NEU: 118.3 KiB → có thể giảm 54.9 KiB với WebP
- Logo HOU: 102.8 KiB → có thể giảm 60.7 KiB với WebP

**Giải pháp:** 
- Convert images sang WebP format
- Hoặc dùng responsive images với `srcset`

### 📝 2. Responsive Images
**Vấn đề:** Images lớn hơn kích thước hiển thị
- Logo hiển thị 116x120 nhưng file 612x608
- Logo hiển thị 116x139 nhưng file 500x502

**Giải pháp:** 
- Tạo nhiều kích thước images (thumbnail, medium, large)
- Dùng `srcset` và `sizes` attributes

### 📝 3. CSS Optimization
**Vấn đề:** CSS vẫn render-blocking (1,210ms tiết kiệm)

**Giải pháp:**
- Inline critical CSS (CSS cho above-the-fold content)
- Defer non-critical CSS
- Hoặc preload CSS file

### 📝 4. Font Optimization
**Đã có:** `display=swap` trong Google Fonts URL
**Có thể thêm:** Preload font files quan trọng

## Kết quả mong đợi sau khi tối ưu:

- **FCP:** Giảm từ 1.4s → ~1.0s
- **LCP:** Giảm từ 1.9s → ~1.2s  
- **Speed Index:** Giảm từ 3.5s → ~2.0s
- **TBT:** Giảm từ 1.08s → ~0.3s
- **Performance Score:** Tăng từ 50 → 70-80

## Test lại sau khi tối ưu:

1. Test trên Google PageSpeed Insights: https://pagespeed.web.dev/
2. Kiểm tra các metrics đã cải thiện
3. Test trên mobile và desktop
