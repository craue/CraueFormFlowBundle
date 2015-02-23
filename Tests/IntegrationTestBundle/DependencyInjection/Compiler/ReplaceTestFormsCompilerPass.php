<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Replaces some forms with legacy ones for Symfony pre-2.7 compatibility.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class ReplaceTestFormsCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritDoc}
	 */
	public function process(ContainerBuilder $container) {
		if (Kernel::VERSION_ID < 20700) {
			$container->findDefinition('integrationTestBundle.form.createTopic')->setClass('Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\LegacyCreateTopicForm');
		}
	}

}
