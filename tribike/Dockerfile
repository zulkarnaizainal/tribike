# Gunakan PHP 8.2 dengan Apache
FROM php:8.2-apache

# Pasang sambungan PostgreSQL untuk PHP
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pgsql pdo_pgsql

# Salin semua fail projek ke direktori pelayan web
COPY . /var/www/html/

# Tetapkan permission
RUN chown -R www-data:www-data /var/www/html

# Dedahkan port 80
EXPOSE 80

# Jalankan Apache bila container mula
CMD ["apache2-foreground"]
