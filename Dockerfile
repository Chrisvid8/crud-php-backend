# Use official PHP image with Apache
FROM php:8.2-apache

# Enable PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copy source code to container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Expose the port Render will use
EXPOSE 10000

# Start Apache in foreground on Render's port
CMD ["apache2-foreground"]
