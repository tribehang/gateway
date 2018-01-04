#!/bin/bash

if [ "$APP_DEBUG" = "true" ]; then
    echo "Setting debugging mode...";
    echo "opcache.validate_timestamps=on" >> /etc/php/7.2/mods-available/opcache.ini
    echo "error_reporting = E_ALL" >> /etc/php/7.2/fpm/php.ini
    echo "display_errors = On" >> /etc/php/7.2/fpm/php.ini
    rm /tmp/__CG__*
fi

echo -n "Waiting for MySQL...";
while ! php artisan migrate:status 2> /dev/null; do echo -n "." && sleep 1; done;

echo "Executing migrations...";
php artisan migrate --no-interaction


echo "Preparing configuration files...";
env | grep APP_ | sed "s/\(.*\)=\(.*\)/env[\1]='\2'/" >> /etc/php/7.2/fpm/pool.d/www.conf
env | grep DB_ | sed "s/\(.*\)=\(.*\)/env[\1]='\2'/" >> /etc/php/7.2/fpm/pool.d/www.conf
env | grep CACHE_ | sed "s/\(.*\)=\(.*\)/env[\1]='\2'/" >> /etc/php/7.2/fpm/pool.d/www.conf
env | grep REDIS_ | sed "s/\(.*\)=\(.*\)/env[\1]='\2'/" >> /etc/php/7.2/fpm/pool.d/www.conf

echo "Starting application (php-fpm)...";
/usr/sbin/php-fpm7.2 -t
service php7.2-fpm start

chmod -R 777 storage/
chmod -R 777 bootstrap/cache/

echo "Starting webserver (nginx)...";
nginx -t
nginx -g "daemon off;"
