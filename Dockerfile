FROM php:7.2-apache

COPY index.php /var/www/html/
COPY upload.php /var/www/html/
COPY restore.php /var/www/html/
COPY dropzone-5.5.0 /var/www/html/dropzone-5.5.0
COPY safebackup.PNG /var/www/html/
COPY favicon.ico /var/www/html/

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN sed -i 's,^post_max_size =.*$,post_max_size = 2048M,' /usr/local/etc/php/php.ini && \
    sed -i 's,^upload_max_filesize =.*$,upload_max_filesize = 2048M,' /usr/local/etc/php/php.ini
USER root