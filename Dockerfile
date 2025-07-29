# Sử dụng image PHP chính thức với Apache
FROM php:7.4-apache

# Đặt thư mục làm việc trong container
WORKDIR /var/www/html

# Sao chép toàn bộ mã nguồn vào container
COPY . .

# Cài đặt các extension PHP cần thiết (thay đổi tùy theo ứng dụng)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Bật chế độ rewrite cho Apache (nếu cần)
RUN a2enmod rewrite

# Mở cổng 80 (cổng mặc định của Apache)
EXPOSE 80

# Chạy Apache khi container khởi động
CMD ["apache2-foreground"]