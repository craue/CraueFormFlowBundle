<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Registration of the bundle via DI.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2018 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class IntegrationTestExtension extends Extension implements PrependExtensionInterface {

	/**
	 * {@inheritDoc}
	 */
	public function load(array $config, ContainerBuilder $container) {
	}

	/**
	 * {@inheritDoc}
	 */
	public function prepend(ContainerBuilder $container) {
		// avoid a deprecation notice regarding logout_on_user_change with Symfony 3.4
		// TODO remove as soon as Symfony >= 4.0 is required
		if (Kernel::MAJOR_VERSION === 3 && Kernel::MINOR_VERSION === 4) {
			$container->prependExtensionConfig('security', array(
				'firewalls' => array(
					'dummy' => array(
						'logout_on_user_change' => true,
					),
				),
			));
		}
	}

}
