# Use official PHP + Apache image
FROM php:8.2-apache

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy backend code into container
COPY . /var/www/html/

# Set working directory to /var/www/html
WORKDIR /var/www/html

# Change Apache DocumentRoot to public folder
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose Render default port
EXPOSE 10000

# Start Apache in foreground
CMD ["apache2-foreground"]
