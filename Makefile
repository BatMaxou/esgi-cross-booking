git = $(shell which git)
php := docker compose run --rm php php -d memory_limit=-1
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
	@$(php) bin/console doctrine:migrations:migrate --no-interaction
.PHONY: database-migration

deploy:
	$(git) pull -fr
	$(MAKE) vendor
	$(MAKE) database-migration
	$(php) bin/console cache:clear
