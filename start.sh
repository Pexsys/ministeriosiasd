#!/bin/bash

# docker network create pexsys && \
docker kill CONTAINER php-apache phpmyadmin mysql
docker rm CONTAINER php-apache phpmyadmin mysql
docker-compose \
  -f ./docker-compose.yml \
  up -d

docker update --memory-swap 4096M -m 4096M --cpus="1.50" mysql;
docker update --memory-swap 1024M -m 1024M --cpus="0.50" php-apache;
docker update --memory-swap 1024M -m 1024M --cpus="0.50" phpmyadmin;
