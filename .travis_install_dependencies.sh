#!/bin/bash

set -euv

export COMPOSER_NO_INTERACTION=1

if [ "${TRAVIS_PHP_VERSION}" = '5.3.3' ]; then
	composer config -g disable-tls true
	composer config -g secure-http false
fi

composer self-update

if [ -n "${MIN_STABILITY:-}" ]; then
	sed -i -e "s/\"minimum-stability\": \"stable\"/\"minimum-stability\": \"${MIN_STABILITY}\"/" composer.json
fi

composer remove --no-update symfony/framework-bundle symfony/form

if [ -n "${SYMFONY_VERSION:-}" ]; then
	composer require --no-update --dev symfony/symfony:"${SYMFONY_VERSION}"
fi

if [ "${USE_DEPS:-}" = 'lowest' ]; then
	COMPOSER_UPDATE_ARGS='--prefer-lowest'
fi

composer update ${COMPOSER_UPDATE_ARGS:-}
