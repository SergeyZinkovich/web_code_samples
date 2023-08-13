PHP := php
CONSOLE := $(PHP) bin/console
THIS_FILE := $(lastword $(MAKEFILE_LIST))

-include .env

show-commands:
	$(CONSOLE)

about:
	$(CONSOLE) about

install: c-inst
	cp .env .env.local
	$(info Configure the parameters in the .env.local file)
	cp docker/php/supervisor/consumer/conf.d/supervisor-consumer.conf supervisor-consumer.local.conf
	$(info Configure the parameters in the supervisor-consumer.local.conf file)
	$(MAKE) -f $(THIS_FILE) dev

deploy: c-inst prod

cs:
	vendor/bin/php-cs-fixer fix --verbose --config=.php_cs/.php-cs-fixer.php

cs-dry-run:
	vendor/bin/php-cs-fixer fix --config=.php_cs/.php-cs-fixer.php --verbose --dry-run

phpstan:
	vendor/bin/phpstan analyse

phpstan-ci:
	bin/console cache:warmup
	vendor/bin/phpstan analyse -c phpstan.ci.neon

phan:
	vendor/bin/phan

phan-strict:
	vendor/bin/phan -S

psalm:
	vendor/bin/psalm --config=psalm.xml --threads=2 --diff --no-suggestions
	vendor/bin/psalm --config=psalm_return_type.xml --threads=2 --diff --no-suggestions

c-inst:
	composer install

admin:
	$(CONSOLE) fos:user:create --super-admin

default-admin:
	$(CONSOLE) fos:user:create admin admin@admin.com admin --super-admin

test:
	bin/phpunit -c phpunit.xml.dist src --process-isolation

test-failing:
	bin/phpunit -c app src --group failing --process-isolation

test-prod: clear-cache-prod

dev: set-permissions cache-dev yarn yarn-dev install-assets cache-dev
	$(MAKE) -f $(THIS_FILE) set-permissions

prod: inc-assets-version set-permissions cache-prod yarn yarn-build install-assets dump
	$(MAKE) -f $(THIS_FILE) set-permissions

set-permissions:
	sudo $(MAKE) -f $(THIS_FILE) set-permissions-wo-sudo

rm-cache:
	sudo rm -rf var/cache/de* && sudo rm -rf var/cache/prod

clear-cache-dev: set-permissions cache-dev
	$(MAKE) -f $(THIS_FILE) set-permissions

clear-cache-prod: set-permissions cache-prod
	$(MAKE) -f $(THIS_FILE) set-permissions

cache-dev:
	$(CONSOLE) cache:clear --env=dev

cache-prod:
	$(CONSOLE) cache:clear --env=prod

update-db:
	$(CONSOLE) doctrine:schema:update --force --dump-sql

reload-db: check-reload-db
	$(CONSOLE) doctrine:database:drop --force
	$(MAKE) -f $(THIS_FILE) create-db

reload-db-test: check-reload-db
	$(CONSOLE) doctrine:database:drop --force --env=test
	$(CONSOLE) doctrine:database:create --env=test
	$(CONSOLE) doctrine:schema:create --env=test

create-db:
	$(CONSOLE) doctrine:database:create

create-schema:
	$(CONSOLE) doctrine:schema:create

check-reload-db:
	@echo "\033[92mAre you sure that you want to reload the database?\033[0m [y/N] " && read ans && [ $${ans:-N} = y ]

dump:
	composer dump-autoload --optimize
	composer dump-env prod

migrate:
	$(CONSOLE) doctrine:migrations:migrate latest

migrate-next:
	$(CONSOLE) doctrine:migrations:migrate next

migrate-prev:
	$(CONSOLE) doctrine:migrations:migrate prev

migrations-diff:
	$(CONSOLE) doctrine:migrations:diff

migrations-status:
	$(CONSOLE) doctrine:migrations:list

inc-assets-version:
	ASSETS_NUM=$$(cat .env.local | grep ASSETS_VERSION= | grep -Eo '[0-9]{1,4}'); \
	sed -i "s/ASSETS_VERSION=$$ASSETS_NUM/ASSETS_VERSION=$$((ASSETS_NUM+1))/" .env.local

dev-wo-permissions: cache-dev yarn-build install-assets

set-permissions-wo-sudo:
	chmod -R ug+rw .
	chmod -R a+rws var/cache var/log public/uploads

docker-up-local:
	docker-compose -f docker-compose.local.yml up --build -d

docker-up-prod:
	docker-compose -f docker-compose.prod.yml up --build -d

docker-down-local:
	docker-compose -f docker-compose.local.yml down

docker-down-prod:
	docker-compose -f docker-compose.prod.yml down

docker-up-osx:
	docker-compose -f docker-compose-osx.yml up --build -d

docker-exec-php:
	docker exec -it $(shell basename $(CURDIR))_php_1 bash

docker-exec-server:
	docker exec -it $(shell basename $(CURDIR))_httpd_1 bash

docker-exec-db:
	docker exec -it $(shell basename $(CURDIR))_db_1 bash

router:
	$(CONSOLE) debug:router

configs:
	$(CONSOLE) debug:config

stop-messenger-workers:
	$(CONSOLE) messenger:stop-workers

supervisor-reload-all: supervisor-reread supervisor-update supervisor-start-all

supervisor-restart-all:
	supervisorctl reread
	supervisorctl update
	supervisorctl restart all

supervisor-reread:
	docker exec -it $(shell basename $(CURDIR))_php_1 supervisorctl reread

supervisor-update:
	docker exec -it $(shell basename $(CURDIR))_php_1 supervisorctl update

supervisor-start-all:
	docker exec -it $(shell basename $(CURDIR))_php_1 supervisorctl start all

supervisor-stop-all:
	docker exec -it $(shell basename $(CURDIR))_php_1 supervisorctl stop all
