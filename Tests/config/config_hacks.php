<?php

use Symfony\Component\HttpKernel\Kernel;

/**
 * @var $container \Symfony\Component\DependencyInjection\ContainerBuilder
 */

// TODO remove as soon as Symfony >= 6 is required
if (Kernel::VERSION_ID >= 40300 && Kernel::VERSION_ID < 60000) {
	$container->loadFromExtension('framework', [
		'router' => [
			'utf8' => true,
		],
	]);
}

// TODO remove as soon as Symfony > 5.2.0 is required
// explicitly enable DBAL to appease RegisterUidTypePass (only for Symfony 5.2.0), see https://github.com/symfony/symfony/issues/39400
if (Kernel::VERSION_ID == 50200) {
	$container->loadFromExtension('doctrine', [
		'dbal' => [],
	]);
}

// TODO put back into config.yml as soon as Symfony >= 5.3 is required, see https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.3.md#frameworkbundle
$container->loadFromExtension('framework', [
	'session' => Kernel::VERSION_ID >= 50300 ? [
		'storage_factory_id' => 'session.storage.factory.mock_file',
	] : [
		'storage_id' => 'session.storage.mock_file',
	],
]);

// TODO clean up as soon as Symfony >= 5.3 is required, see https://github.com/symfony/symfony/pull/41247
// TODO remove as soon as Symfony >= 6.2 is required, see https://github.com/symfony/symfony/pull/47890
$container->loadFromExtension('security', Kernel::VERSION_ID >= 60200 ? [] : (Kernel::VERSION_ID >= 50300 ? [
	'enable_authenticator_manager' => true,
] : [
	'firewalls' => [
		'dummy' => [
			'anonymous' => true,
		],
	],
]));

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
