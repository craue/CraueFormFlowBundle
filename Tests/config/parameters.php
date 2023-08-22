<?php

/*
 * Only set parameters if they aren't already defined. This allows using environment variables (e.g. set by GitHub Actions) and fallback values.
 */

$defaultParameters = [
	'db.driver' => null,
// 	'db.driver' => 'pdo_mysql',
// 	'db.driver' => 'pdo_sqlite',
	'db.host' => '127.0.0.1',
	'db.port' => null,
	'db.name' => 'test',
	'db.user' => 'test',
	'db.password' => null,
	'db.path' => $container->getParameter('kernel.cache_dir') . '/sqlite.db',
];

foreach ($defaultParameters as $name => $defaultValue) {
	if (!$container->hasParameter($name)) {
		$envValue = getenv(sprintf('PARAM_%s', strtoupper(strtr($name, '.', '_'))));
		$container->setParameter($name, $envValue !== false && $envValue !== '' ? $envValue : $defaultValue);
	}
}
