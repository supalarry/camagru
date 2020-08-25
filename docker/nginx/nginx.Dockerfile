FROM nginx

ADD docker/nginx/conf/vhost.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/camagru
