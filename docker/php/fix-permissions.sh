#!/bin/sh
set -e

chown -R $(id -u):$(id -g) /var/www/html/storage

chmod 777 -R /var/www/html/storage

exec "$@"