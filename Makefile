.PHONY: test lint phpunit phpcs phpstan markdownlint all example install

all: test lint

lint: phpcs phpstan markdownlint

test: phpunit

install:
	composer install

vendor/bin/phpcs:
	composer install

phpcs: vendor/bin/phpcs
	-vendor/bin/phpcs -s

vendor/bin/phpunit:
	composer install

phpunit: vendor/bin/phpunit
	-vendor/bin/phpunit

vendor/bin/phpstan:
	composer install

phpstan: vendor/bin/phpstan
	-vendor/bin/phpstan analyse

node_modules/.bin/markdownlint:
	npm install

markdownlint: node_modules/.bin/markdownlint
	-node_modules/.bin/markdownlint .

example:
	php example.php
