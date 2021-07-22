.PHONY: test lint phpunit phpcs phpstan all example install

all: test lint

lint: phpcs phpstan

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

example:
	php example.php
