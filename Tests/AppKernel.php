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
			$configFile = $this->getProjectDir() . '/config/' . $configFile;
		}

		if (!file_exists($configFile)) {
			throw new \RuntimeException(sprintf('The config file "%s" does not exist.', $configFile));
		}

		$this->configFile = $configFile;
	}

	public function registerBundles() : iterable {
		return [
			new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
			new \Symfony\Bundle\TwigBundle\TwigBundle(),
			new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
			new \Craue\FormFlowBundle\CraueFormFlowBundle(),
			new \Craue\FormFlowBundle\Tests\IntegrationTestBundle\IntegrationTestBundle(),
		];
	}

	public function registerContainerConfiguration(LoaderInterface $loader): void
    {
		$loader->load($this->configFile);
	}

    public function getCacheDir(): string
    {
        return sprintf('%scache', $this->getBaseDir());
    }

    public function getLogDir(): string
    {
        return sprintf('%slog', $this->getBaseDir());
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    private function getBaseDir(): string
    {
        return sprintf('%s/craue-form-flow-bundle/var/', sys_get_temp_dir());
    }
}
