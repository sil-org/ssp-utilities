test: composer
	docker compose run --rm cli ./vendor/bin/phpunit tests/

composer:
	docker compose run --rm --user "0:0" cli composer install

composerupdate:
	docker compose run --rm --user "0:0" cli composer update
