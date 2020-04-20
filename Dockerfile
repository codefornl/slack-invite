FROM php:7.0-apache

RUN apt-get update -y && \
    apt-get install ca-certificates

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

USER www-data

ENV SLACK_TOKEN foo
USER root
