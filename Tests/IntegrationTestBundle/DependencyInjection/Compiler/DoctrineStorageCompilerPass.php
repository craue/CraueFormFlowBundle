<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Registration of services needed to use the {@link DoctrineStorage} implementation.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2024 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class DoctrineStorageCompilerPass implements CompilerPassInterface {

	public function process(ContainerBuilder $container) : void {
		if ($container->has('doctrine.dbal.default_connection')) {
			$loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));
			$loader->load('doctrine_storage.xml');
		}
	}

}
