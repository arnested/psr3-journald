.PHONY: test lint phpunit phpcs phpstan markdownlint all example

all: test lint

lint: phpcs phpstan markdownlint

test: phpunit

phpcs:
	-vendor/bin/phpcs -s

phpunit:
	-vendor/bin/phpunit

phpstan:
	-vendor/bin/phpstan analyse

node_modules/.bin/markdownlint:
	npm  install

markdownlint: node_modules/.bin/markdownlint
	-node_modules/.bin/markdownlint .

example:
	php example.php
