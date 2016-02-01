<?php

/*
 * Only set parameters if they aren't already defined. This allows using environment variables (e.g. set by Travis) and fallback values.
 * See http://symfony.com/doc/current/cookbook/configuration/external_parameters.html#environment-variables for details.
 */

$defaultParameters = array(
	'db.driver' => null,
// 	'db.driver' => 'pdo_mysql',
// 	'db.driver' => 'pdo_sqlite',
	'db.host' => '127.0.0.1',
	'db.port' => null,
	'db.name' => 'test',
	'db.user' => 'travis',
	'db.password' => null,
	'db.path' => $container->getParameter('kernel.cache_dir') . '/sqlite.db',
);

foreach ($defaultParameters as $name => $value) {
	if (!$container->hasParameter($name)) {
		$container->setParameter($name, $value);
	}
}
