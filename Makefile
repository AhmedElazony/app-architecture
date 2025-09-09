NVM_USE = . ~/.nvm/nvm.sh && nvm use --lts
SHELL := /bin/bash

# SSL settings
SSL_DIR = ./docker/nginx/ssl
SSL_KEY = $(SSL_DIR)/private.key
SSL_CERT = $(SSL_DIR)/public.crt

# User/Group env vars
UID := $(shell id -u)
GID := $(shell id -g)

# MySql Backup config
# Targets
.PHONY: images install certs deploy undeploy

images:
	@docker compose build

install:
	docker compose run --rm -u "$(UID):$(GID)" app composer install && \
	cp .env.example .env && \
	docker compose run --rm -u "$(UID):$(GID)" app php artisan key:generate

certs:
	@if [ -f $(SSL_KEY) ] && [ -f $(SSL_CERT) ]; then \
		echo "SSL certificate already exists:"; \
		echo "   → $(SSL_KEY)"; \
		echo "   → $(SSL_CERT)"; \
	else \
		mkdir -p $(SSL_DIR); \
		openssl req -x509 -nodes -days 365 \
			-newkey rsa:2048 \
			-keyout $(SSL_KEY) \
			-out $(SSL_CERT) \
			-subj "/C=OM/ST=Muscat/L=Oman/O=AI/CN=localhost"; \
		echo "✅ SSL certificate generated."; \
	fi

bash:
	docker compose run --rm -u "${UID}:${GID}" app bash

fix-permissions:
	@docker compose run --rm -u "$(UID):$(GID)" app /var/www/html/docker/php/fix-permissions.sh