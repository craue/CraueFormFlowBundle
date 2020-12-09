<?php

use Symfony\Component\HttpKernel\Kernel;

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
