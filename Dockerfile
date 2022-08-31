FROM php:8.0-alpine

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer