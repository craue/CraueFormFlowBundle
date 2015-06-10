<?php

namespace Craue\FormFlowBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Replaces the setRequestStack method call of the FormFlow base class to pass a Request instance in case the
 * RequestStack is not available. This is needed for Symfony 2.3 compatibility.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class LegacyRequestCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritDoc}
	 */
	public function process(ContainerBuilder $container) {
		// TODO remove as soon as Symfony >= 2.4 is required
		if (!$container->hasDefinition('request_stack')) {
			$formFlow = $container->findDefinition('craue.form.flow');
			$formFlow->removeMethodCall('setRequestStack');
			$formFlow->addMethodCall('setRequestStack', array(new Reference('request')));
		}
	}

}
