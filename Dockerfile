FROM php:8.3-apache

# Install ekstensi PHP yang umum dibutuhkan
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Aktifkan mod_rewrite Apache (wajib untuk Laravel/routing)
RUN a2enmod rewrite

# Arahkan DocumentRoot ke folder public (khusus Laravel; hapus '/public' jika PHP native)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy file proyek ke dalam container
WORKDIR /var/www/html
COPY . .

# Install dependencies & atur permission folder storage
RUN composer install --no-dev --optimize-autoloader
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
