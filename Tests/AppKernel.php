<?php

namespace Craue\FormFlowBundle\Tests;

require_once __DIR__.'/bootstrap.php';

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel {

	private $config;

	public function __construct($config) {
		parent::__construct('test', true);

		$fs = new Filesystem();
		if (!$fs->isAbsolutePath($config)) {
			$config = __DIR__.'/config/'.$config;
		}

		if (!file_exists($config)) {
			throw new \RuntimeException(sprintf('The config file "%s" does not exist.', $config));
		}

		$this->config = $config;
	}

	public function registerBundles() {
		return array(
			new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new \Symfony\Bundle\TwigBundle\TwigBundle(),
			new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
			new \Craue\FormFlowBundle\CraueFormFlowBundle(),
			new \Craue\FormFlowBundle\Tests\IntegrationTestBundle\IntegrationTestBundle(),
		);
	}

	public function registerContainerConfiguration(LoaderInterface $loader) {
		$loader->load($this->config);
	}

	public function serialize() {
		return $this->config;
	}

	public function unserialize($config) {
		$this->__construct($config);
	}

}
