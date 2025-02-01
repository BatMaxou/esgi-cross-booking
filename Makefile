git = $(shell which git)
php := docker compose run --rm php php -d memory_limit=-1
console := $(php) bin/console
composer := $(shell docker compose run --rm php which composer)
qa := docker run --rm -t -v `pwd`:/project --workdir="/project" jakzal/phpqa:php8.3

vendor:
	@$(php) $(composer) install
.PHONY: vendor

fixcs:
	@$(qa) php-cs-fixer fix --config=./.devops/lint/phpcs/.php-cs-fixer.php
.PHONY: fixcs

phpcs:
	@$(qa) php-cs-fixer fix --config=./.devops/lint/phpcs/.php-cs-fixer.php --dry-run
.PHONY: fixcs

phpstan:
	@$(php) vendor/bin/phpstan analyse src --configuration=./.devops/lint/phpstan/config.neon
.PHONY: phpstan

database-migration:
	@$(console) doctrine:migrations:migrate --no-interaction
.PHONY: database-migration

pretests:
	@$(console) --env=test doctrine:database:drop --if-exists -f
	@$(console) --env=test doctrine:database:create
	@$(console) --env=test doctrine:schema:update --force
	@$(console) --env=test cache:clear --no-warmup
.PHONY: pretests

tests: pretests
	@$(php) bin/phpunit
.PHONY: tests

setup-messenger:
	@$(console) messenger:setup-transports

install:
	docker compose up -d
	$(MAKE) vendor
	$(console) doctrine:database:create --if-not-exists
	$(MAKE) database-migration
	$(MAKE) setup-messenger
	docker compose run --rm php chmod -R 777 public/images

fixtures:
	$(console) doctrine:fixtures:load --no-interaction

deploy:
	docker compose down
	$(git) pull -fr
	docker compose up -d
	$(MAKE) vendor
	$(MAKE) database-migration
	$(php) bin/console cache:clear
