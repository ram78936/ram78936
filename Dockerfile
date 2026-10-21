FROM php:8.2-apache
RUN docker-php-ext-install mysqli \
 && a2enmod headers \
 && printf 'ServerTokens Prod\nServerSignature Off\n' > /etc/apache2/conf-available/security-hardening.conf \
 && a2enconf security-hardening
WORKDIR /var/www/html
COPY foodgo/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html \
 && find /var/www/html -type d -exec chmod 755 {} \; \
 && find /var/www/html -type f -exec chmod 644 {} \;
EXPOSE 80
