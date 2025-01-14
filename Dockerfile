# 使用官方 PHP 8.2 CLI 映像檔作為基底
FROM php:8.2-cli

# 設定工作目錄
WORKDIR /var/www/html

# 安裝系統套件和 PHP 擴展
RUN apt-get update && \
    apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    sockets \
    && pecl install redis && \
    docker-php-ext-enable redis

# 安裝 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 複製專案文件
COPY . .

# 安裝 PHP 依賴
RUN composer install --no-dev --optimize-autoloader

# # 生成應用密鑰
# RUN php artisan key:generate

# 設定正確的權限
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 開放端口
EXPOSE 8000

# 啟動 Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]