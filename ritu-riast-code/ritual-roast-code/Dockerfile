# official php image with apache
FROM php:7.4-apache

# Install mysqli and upgrade (required lib by our code to do database operations)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apt-get update && apt-get upgrade -y

# Copy code into image
COPY . /var/www/html

# Apache will run on port 80
EXPOSE 80