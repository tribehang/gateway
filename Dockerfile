FROM tribehang/php-container

RUN apt-get -y update && \
    apt-get -y --no-install-recommends install \
    php-soap

ADD . /var/www/html

RUN composer config --global github-oauth.github.com 844e38651cc95a251add0dfdfd1f49072fab26ff && \
    composer install --no-dev --no-interaction --no-progress --optimize-autoloader

EXPOSE 80 443

CMD ["/bin/bash", "docker/run.sh"]