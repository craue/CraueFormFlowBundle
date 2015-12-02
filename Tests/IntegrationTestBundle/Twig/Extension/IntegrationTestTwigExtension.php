<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Twig\Extension;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class IntegrationTestTwigExtension extends \Twig_Extension {

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'integration_test';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFunctions() {
		if (version_compare(\Twig_Environment::VERSION, '1.12', '<')) {
			return array(
				'hasFormStart' => new \Twig_Function_Method($this, 'hasFormStart', array('needs_environment' => true)),
			);
		}

		return array(
			new \Twig_SimpleFunction('hasFormStart', array($this, 'hasFormStart'), array('needs_environment' => true)),
		);
	}

	/**
	 * @return boolean If the Twig function "form_start" is available.
	 */
	public function hasFormStart(\Twig_Environment $env) {
		return $env->getFunction('form_start') !== false;
	}

}
