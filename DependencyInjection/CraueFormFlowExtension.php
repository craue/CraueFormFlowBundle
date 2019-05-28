<?php

namespace Craue\FormFlowBundle\DependencyInjection;

use Craue\FormFlowBundle\Form\FormFlowInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Registration of the extension via DI.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2019 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CraueFormFlowExtension extends Extension
{
	const FORM_FLOW_TAG = 'craue.form.flow';

	/**
	 * {@inheritDoc}
	 */
	public function load(array $config, ContainerBuilder $container)
	{
		$loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
		$loader->load('form_flow.xml');
		$loader->load('twig.xml');
		$loader->load('util.xml');

		$childDefinition = $container->registerForAutoconfiguration(FormFlowInterface::class)
			->addTag(self::FORM_FLOW_TAG);

		if ($this->symfonySupportsMethodCallsOnAutoconfigure()) {
			$childDefinition->setMethodCalls($container->findDefinition('craue.form.flow')->getMethodCalls());
		}
	}

	/**
	 * @see https://github.com/symfony/dependency-injection/commit/e546fec37cb2692565df5f1da8ff3e865b6babd5#diff-4388d835b7c438233f9e1fa1ca067473
	 * @return bool
	 */
	private function symfonySupportsMethodCallsOnAutoconfigure()
	{
		return Kernel::VERSION_ID >= 40100;
	}
}
