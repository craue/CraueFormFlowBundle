#!/bin/bash

set -euv

#####
# run satooshi/php-coveralls from a separate project due to Symfony 3.x incompatiblity
#####

# disable Xdebug for running Composer
phpenv config-rm xdebug.ini

composer -n init --require-dev satooshi/php-coveralls:~0.6

# enforce Symfony 2 to be used while avoiding to replace already installed 2.x with 2.y
if [[ -n "${SYMFONY_VERSION:-}" && "${SYMFONY_VERSION}" == 2* ]]; then
	composer require --no-update --dev symfony/symfony:"${SYMFONY_VERSION}"
else
	composer require --no-update --dev symfony/symfony:~2.0
fi

composer update --prefer-dist

php vendor/bin/coveralls -v
