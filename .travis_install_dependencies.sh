#!/bin/bash

set -euv

export COMPOSER_NO_INTERACTION=1
composer self-update

case "${DEPS:-}" in
	'lowest')
		COMPOSER_UPDATE_ARGS='--prefer-lowest'
		;;
	'unmodified')
		# don't modify dependencies, install them as defined
		;;
	*)
		if [ -n "${MIN_STABILITY:-}" ]; then
			composer config minimum-stability "${MIN_STABILITY}"
		fi

		if [ -n "${SYMFONY_VERSION:-}" ]; then
			composer require --no-update --dev symfony/symfony:"${SYMFONY_VERSION}"
		fi
esac

if [ -n "${WITH_STATIC_ANALYSIS:-}" ]; then
	composer require --no-update --dev "phpstan/phpstan:^0.12"
fi

# TODO remove as soon as Symfony >= 4.2 is required
if [ -n "${WITH_TRANSLATION_CONTRACTS:-}" ]; then
	composer require --no-update --dev "symfony/translation-contracts:~1.1"
fi

composer update ${COMPOSER_UPDATE_ARGS:-}
