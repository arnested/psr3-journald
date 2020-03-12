.PHONY: test lint phpunit phpcs phpstan all example

all: test lint

lint: phpcs phpstan

test: phpunit

phpcs:
	vendor/bin/phpcs

phpunit:
	vendor/bin/phpunit

phpstan:
	vendor/bin/phpstan analyse

example:
	php example.php
