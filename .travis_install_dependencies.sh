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
			sed -i -e "s/\"minimum-stability\": \"stable\"/\"minimum-stability\": \"${MIN_STABILITY}\"/" composer.json
		fi

		composer remove --no-update symfony/form symfony/http-kernel symfony/translation symfony/yaml

		if [ -n "${SYMFONY_VERSION:-}" ]; then
			composer require --no-update --dev symfony/symfony:"${SYMFONY_VERSION}"
		fi
esac

composer update ${COMPOSER_UPDATE_ARGS:-}
