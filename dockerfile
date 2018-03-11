FROM php:7.2.3-fpm

RUN pecl install swoole-2.1.1 && docker-php-ext-enable swoole