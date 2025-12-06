# Use official PHP image with Apache web server
FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Copy your PHP app files into the web server directory
COPY . /var/www/html/

# Expose port 80 to access the app in the browser
EXPOSE 80

# Start Apache server when the container runs
CMD ["apache2-foreground"]
