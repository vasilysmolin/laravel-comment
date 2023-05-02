FROM nginx:alpine

RUN rm /etc/nginx/conf.d/*

WORKDIR /var/www/laravel-comment/

COPY ./images/nginx/nginx.conf /etc/nginx/
COPY ./images/nginx/production.conf /etc/nginx/conf.d/

COPY ./public /var/www/laravel-comment/public
