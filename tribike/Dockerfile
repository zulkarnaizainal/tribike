# Guna image PHP 8.2 dengan Apache
FROM php:8.2-apache

# Pasang sambungan PHP yang diperlukan untuk MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Salin semua fail projek ke direktori pelayan web Apache
COPY . /var/www/html/

# Tetapkan permission fail
RUN chown -R www-data:www-data /var/www/html

# Dedahkan port 80 untuk HTTP
EXPOSE 80

# Mula Apache
CMD ["apache2-foreground"]
