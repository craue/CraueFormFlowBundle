<?php

namespace Craue\FormFlowBundle;

use Craue\FormFlowBundle\DependencyInjection\Compiler\LoadTranslationsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CraueFormFlowBundle extends Bundle {

	/**
	 * {@inheritDoc}
	 */
	public function build(ContainerBuilder $container) {
		parent::build($container);
		$container->addCompilerPass(new LoadTranslationsCompilerPass());
	}

}
