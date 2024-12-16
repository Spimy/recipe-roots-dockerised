FROM php:8.2.12-apache

RUN docker-php-ext-install pdo_mysql
RUN apt-get update
RUN apt-get install -y msmtp ca-certificates

COPY /conf/sendmail/msmtprc.conf /etc/msmtprc.conf
COPY /conf/apache/sites-available /etc/apache2/sites-available
COPY /conf/ssl /etc/letsencrypt

RUN chown www-data /etc/msmtprc.conf
RUN chmod 600 /etc/msmtprc.conf
RUN chown www-data:www-data /var/www/html/recipe-roots/public/uploads
RUN chmod 755 /var/www/html/recipe-roots/public/uploads
RUN find /var/www/html/recipe-roots/public/uploads -type f -exec chmod -x {} \;

RUN echo "ServerName recipe-roots.spimy.dev" >> /etc/apache2/apache2.conf

CMD apache2-foreground

RUN a2enmod rewrite
RUN a2enmod mime
RUN a2enmod ssl
RUN a2ensite recipe-roots
RUN a2ensite recipe-roots-ssl
RUN a2dissite 000-default
RUN service apache2 restart