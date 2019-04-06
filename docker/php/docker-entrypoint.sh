#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-production"
	if [ "$APP_ENV" != 'prod' ]; then
		PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-development"
	fi
	ln -sf "$PHP_INI_RECOMMENDED" "$PHP_INI_DIR/php.ini"

	mkdir -p var/cache var/log
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var

	if [ "$APP_ENV" != 'prod' ]; then
		composer install --prefer-dist --no-progress --no-suggest --no-interaction
	fi

	#echo "Waiting for db to be ready..."
	until bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
		sleep 1
	done
	
	if [ "$APP_ENV" != 'prod' ]; then
		# Let clear the cash
		bin/console cache:clear --no-warmup 
		bin/console doctrine:cache:clear-metadata 
		bin/console doctrine:cache:clear-query
		bin/console doctrine:cache:clear-result
		# Lets make sure the database is in order
		bin/console doctrine:schema:update --force --no-interaction
		# Lets reset the database and load example data
		bin/console doctrine:fixtures:load --no-interaction 
	fi
	
	# Let update the docs to show the latest chages
	bin/console api:swagger:export --output=/srv/api/public/schema/openapi.yaml --yaml --spec-version=3
fi

exec docker-php-entrypoint "$@"
