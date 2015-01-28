<?php

namespace Craue\FormFlowBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Replaces the form extensions with legacy ones for Symfony pre-2.7 compatibility.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class ReplaceFormExtensionsCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritDoc}
	 */
	public function process(ContainerBuilder $container) {
		if (Kernel::VERSION_ID < 20700) {
			$container->findDefinition('craue.form.flow.form_extension')->setClass('Craue\FormFlowBundle\Form\Extension\LegacyFormFlowFormExtension');
			$container->findDefinition('craue.form.flow.step_field_extension')->setClass('Craue\FormFlowBundle\Form\Extension\LegacyFormFlowStepFieldExtension');
		}
	}

}
