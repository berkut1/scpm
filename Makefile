up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear docker-pull docker-build docker-up scpsc-init
init-no-fixtures: docker-down-clear docker-pull docker-build docker-up scpsc-init-no-fixtures
test: scpsc-test
init-no-net: docker-down-clear docker-build docker-up scpsc-init

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

scpsc-init: scpsc-composer-install scpsc-assets-install scpsc-migrations scpsc-fixtures scpsc-generate-ssl-key

scpsc-init-no-fixtures: scpsc-composer-install scpsc-assets-install scpsc-migrations scpsc-generate-ssl-key

scpsc-composer-install:
	docker-compose run --rm scpsc-php-cli composer install

# увеличиваем временно память чтобы выполнился весь скрипт, для вызываем композер через php и полный путь
scpsc-composer-install-memory:
	docker-compose run --rm scpsc-php-cli php -d memory_limit=256M /bin/composer install

scpsc-assets-install:
	docker-compose run --rm scpsc-node yarn install

scpsc-migrations:
	docker-compose run --rm scpsc-php-cli php bin/console doctrine:migrations:migrate --no-interaction

scpsc-fixtures:
	docker-compose run --rm scpsc-php-cli php bin/console doctrine:fixtures:load --no-interaction

scpsc-test:
	docker-compose run --rm scpsc-php-cli php bin/phpunit

scpsc-clear-cache:
	docker-compose run --rm scpsc-php-cli php -d memory_limit=256M bin/console cache:clear

scpsc-generate-ssl-key:
	docker-compose run --rm scpsc-php-cli php bin/console lexik:jwt:generate-keypair --skip-if-exists

apidocs:
	docker-compose run --rm scpsc-php-cli php bin/console api:docs

#make cli p="composer install"
cli:
	docker-compose run --rm scpsc-php-cli $(p)

node:
	docker-compose run --rm scpsc-node $(p)

devassets:
	docker-compose run --rm scpsc-node yarn encore dev

prodassets:
	docker-compose run --rm scpsc-node yarn encore production