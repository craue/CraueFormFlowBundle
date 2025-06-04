<?php

use Symfony\Component\HttpKernel\Kernel;

/**
 * @var $container \Symfony\Component\DependencyInjection\ContainerBuilder
 */

if (!empty($_ENV['DB_DSN'])) {
	$container->loadFromExtension('doctrine', [
		'dbal' => [
			'url' => $_ENV['DB_DSN'],
		],
	]);
}

// TODO remove as soon as Symfony >= 6 is required
if (Kernel::VERSION_ID >= 40300 && Kernel::VERSION_ID < 60000) {
	$container->loadFromExtension('framework', [
		'router' => [
			'utf8' => true,
		],
	]);
}

// TODO remove as soon as Symfony >= 6.2 is required, see https://github.com/symfony/symfony/pull/47890
$container->loadFromExtension('security', Kernel::VERSION_ID >= 60200 ? [] : [
	'enable_authenticator_manager' => true,
]);

// TODO remove as soon as Symfony >= 7 is required, see https://github.com/symfony/symfony/blob/6.4/UPGRADE-6.4.md#frameworkbundle
if (Kernel::VERSION_ID >= 60400 && Kernel::VERSION_ID < 70000) {
	$container->loadFromExtension('framework', [
		'handle_all_throwables' => true,
		'php_errors' => [
			'log' => true,
		],
		'session' => [
			'cookie_secure' => 'auto',
			'cookie_samesite' => 'lax',
		],
		'validation' => [
			'email_validation_mode' => 'html5',
		],
	]);
}

// TODO remove as soon as Symfony >= 7 is required, see https://github.com/symfony/symfony/blob/6.1/UPGRADE-6.1.md#frameworkbundle
if (Kernel::VERSION_ID < 70000) {
	$container->loadFromExtension('framework', [
		'http_method_override' => false,
	]);
}

// TODO remove as soon as Symfony >= 7.3 is required
if (Kernel::VERSION_ID >= 70300) {
	$container->loadFromExtension('framework', [
		'property_info' => [
			'with_constructor_extractor' => true,
		],
	]);
}
