<?php

namespace Craue\FormFlowBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

/**
 * Explicitly registers translation files in their uncommon location.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class LoadTranslationsCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritDoc}
	 */
	public function process(ContainerBuilder $container) {
		$dir = __DIR__ . '/../../FormFlow/Resources/translations';

		// taken roughly from https://github.com/symfony/symfony/blob/ce15db564736d7a0cf02a0db688a0ee101959cb5/src/Symfony/Bundle/FrameworkBundle/DependencyInjection/FrameworkExtension.php#L650
		$container->addResource(new DirectoryResource($dir));

		$finder = Finder::create()
			->files()
			->in($dir)
			->filter(function (\SplFileInfo $file) {
				$basename = $file->getBasename();
				return substr_count($basename, '.') === 2 && preg_match('/\.\w+$/', $basename);
			})
		;

		$translator = $container->findDefinition('translator');

		foreach ($finder as $file) {
			list($domain, $locale, $format) = explode('.', $file->getBasename(), 3);
			$translator->addMethodCall('addResource', array($format, (string) $file, $locale, $domain));
		}
	}

}
