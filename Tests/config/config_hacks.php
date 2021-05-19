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

// TODO put back into config.yml as soon as Symfony >= 5.3 is required, see https://github.com/symfony/symfony/pull/41247
$container->loadFromExtension('security', Kernel::VERSION_ID >= 50300 ? [
	'enable_authenticator_manager' => true,
] : [
	'firewalls' => [
		'dummy' => [
			'anonymous' => true,
		],
	],
]);
