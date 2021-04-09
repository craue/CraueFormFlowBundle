<?php

namespace Craue\FormFlowBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * For services requiring session access, inject the Session service directly in case the RequestStack service does not provide the session.
 * This is needed for Symfony < 5.3 compatibility.
 * See https://github.com/symfony/symfony/pull/38616
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2021 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class LegacySessionCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritDoc}
	 */
	public function process(ContainerBuilder $container) : void {
		// TODO remove as soon as Symfony >= 5.3 is required
		$container->findDefinition('craue.form.flow.storage_default')->replaceArgument(0, new Reference('session'));
	}

}
