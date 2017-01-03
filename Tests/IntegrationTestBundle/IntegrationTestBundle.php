<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle;

use Craue\FormFlowBundle\Tests\IntegrationTestBundle\DependencyInjection\Compiler\DoctrineStorageCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class IntegrationTestBundle extends Bundle {

	/**
	 * {@inheritDoc}
	 */
	public function build(ContainerBuilder $container) {
		parent::build($container);

		$container->addCompilerPass(new DoctrineStorageCompilerPass());
	}

}
