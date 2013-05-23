#!/bin/sh

wget http://getcomposer.org/composer.phar

php composer.phar config -g preferred-install source

sed -i -e "s/\"minimum-stability\": \"stable\"/\"minimum-stability\": \"${MIN_STABILITY}\"/" composer.json

php composer.phar --no-interaction --no-update require symfony/framework-bundle:${SYMFONY_VERSION} symfony/form:${SYMFONY_VERSION}
php composer.phar --no-interaction --no-update require --dev sensio/framework-extra-bundle:${SYMFONY_VERSION} symfony/symfony:${SYMFONY_VERSION}
php composer.phar --no-interaction update
