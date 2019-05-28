<?php

namespace Craue\FormFlowBundle\DependencyInjection;

use Craue\FormFlowBundle\Form\FormFlowInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Registration of the extension via DI.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2019 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CraueFormFlowExtension extends Extension {

	const FORM_FLOW_TAG = 'craue.form.flow';

	/**
	 * {@inheritDoc}
	 */
	public function load(array $config, ContainerBuilder $container) {
		$loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
		$loader->load('form_flow.xml');
		$loader->load('twig.xml');
		$loader->load('util.xml');

		$container->registerForAutoconfiguration(FormFlowInterface::class)
			->addTag(self::FORM_FLOW_TAG)
			->setMethodCalls($container->getDefinition('craue.form.flow')->getMethodCalls())
		;
	}
}
