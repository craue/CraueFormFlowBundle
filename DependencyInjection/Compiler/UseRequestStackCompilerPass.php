<?php

namespace Craue\FormFlowBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * In the service definition for the FormFlow base class, replaces the setRequest method call to pass a RequestStack instance if available.
 * This is needed for Symfony 3.0 compatibility.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class UseRequestStackCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritDoc}
	 */
	public function process(ContainerBuilder $container) {
		if ($container->hasDefinition('request_stack')) {
			$formFlow = $container->findDefinition('craue.form.flow');
			$formFlow->removeMethodCall('setRequest');
			$formFlow->addMethodCall('setRequest', array(new Reference('request_stack')));
		}
	}

}
