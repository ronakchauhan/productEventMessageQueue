# Use the official PHP image as the base image
FROM php:7.4-cli

# Set the working directory inside the container
WORKDIR /app

# Copy the application files to the container
COPY . /app

# Install any necessary dependencies
RUN apt-get update \
    && apt-get install -y libzip-dev zip \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

# Install Composer (dependency manager for PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install project dependencies using Composer
RUN composer install --no-interaction

# Expose the port on which the application will run (if needed)
# EXPOSE 8080

# The entry point command to execute the test_run.php script
CMD ["php", "test_run.php"]
