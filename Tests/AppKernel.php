<?php

namespace Craue\FormFlowBundle\Tests;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel {

	private $configFile;

	public function __construct($environment, $configFile) {
		parent::__construct($environment, false);

		$fs = new Filesystem();
		if (!$fs->isAbsolutePath($configFile)) {
			$configFile = __DIR__ . '/config/' . $configFile;
		}

		if (!file_exists($configFile)) {
			throw new \RuntimeException(sprintf('The config file "%s" does not exist.', $configFile));
		}

		$this->configFile = $configFile;
	}

	public function registerBundles() {
		return [
			new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
			new \Symfony\Bundle\TwigBundle\TwigBundle(),
			new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
			new \Craue\FormFlowBundle\CraueFormFlowBundle(),
			new \Craue\FormFlowBundle\Tests\IntegrationTestBundle\IntegrationTestBundle(),
		];
	}

	public function registerContainerConfiguration(LoaderInterface $loader) {
		$loader->load($this->configFile);
	}

	public function getCacheDir() {
		if (array_key_exists('CACHE_DIR', $_ENV)) {
			return $_ENV['CACHE_DIR'] . DIRECTORY_SEPARATOR . $this->environment;
		}

		return parent::getCacheDir();
	}

	public function getLogDir() {
		if (array_key_exists('LOG_DIR', $_ENV)) {
			return $_ENV['LOG_DIR'] . DIRECTORY_SEPARATOR . $this->environment;
		}

		return parent::getLogDir();
	}

	public function serialize() {
		return serialize([$this->environment, $this->configFile]);
	}

	public function unserialize($data) {
		list($environment, $configFile) = unserialize($data);
		$this->__construct($environment, $configFile);
	}

}
