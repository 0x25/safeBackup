FROM php:7.4-apache

RUN mkdir -p /var/www/html/uploads

COPY index.php /var/www/html/
COPY upload.php /var/www/html/
COPY dropzone-5.5.0 /var/www/html/dropzone-5.5.0
COPY safebackup.PNG /var/www/html/
COPY favicon.ico /var/www/html/

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
sed -i 's,^post_max_size =.*$,post_max_size = 2048M,' $PHP_INI_DIR/php.ini && \
sed -i 's,^upload_max_filesize =.*$,upload_max_filesize = 2048M,' $PHP_INI_DIR/php.ini && \
sed -i 's,^memory_limit = .*$,memory_limit = -1,' $PHP_INI_DIR/php.ini && \
sed -i 's,out.txt,php://stdout,' /var/www/html/upload.php

RUN chown -R www-data:www-data /var/www/ && \
chmod 766 /var/www/html/uploads

USER root
