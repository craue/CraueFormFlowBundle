<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Kernel;

$loader = require __DIR__.'/../vendor/autoload.php';
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

if (Kernel::VERSION_ID >= 20600) {
	// https://github.com/symfony/symfony/issues/12973#issuecomment-67814213
	ErrorHandler::register(null, false)->traceAt(E_USER_DEPRECATED);
}
