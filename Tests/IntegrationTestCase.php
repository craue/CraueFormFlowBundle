<?php

namespace Craue\FormFlowBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Twig\Environment;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class IntegrationTestCase extends WebTestCase {

	const ENV_FLOWS_WITH_AUTOCONFIGURATION = 'flows_with_autoconfiguration';
	const ENV_FLOWS_WITH_PARENT_SERVICE = 'flows_with_parent_service';

	/**
	 * @var AbstractBrowser|Client|null
	 * TODO remove Client type as soon as Symfony >= 4.3 is required
	 */
	protected static $client;

	public function getEnvironmentConfigs() {
		$testData = [];

		foreach ([self::ENV_FLOWS_WITH_AUTOCONFIGURATION, self::ENV_FLOWS_WITH_PARENT_SERVICE] as $env) {
			$testData[] = [$env, sprintf('config_%s.yml', $env)];
		}

		return $testData;
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function createKernel(array $options = []) {
		$environment = $options['environment'] ?? self::ENV_FLOWS_WITH_AUTOCONFIGURATION;
		$configFile = $options['config'] ?? sprintf('config_%s.yml', $environment);

		return new AppKernel($environment, $configFile);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function setUp() {
		$this->setUpClient();
	}

	protected function setUpClient() {
		static::$client = static::createClient();
	}

	/**
	 * @param string $id The service identifier.
	 * @return object The associated service.
	 */
	protected function getService($id) {
		return static::$kernel->getContainer()->get($id);
	}

	/**
	 * @return Environment
	 */
	protected function getTwig() {
		return $this->getService('twig');
	}

	/**
	 * @param string $route
	 * @param array $parameters
	 * @return string URL
	 */
	protected function url($route, array $parameters = []) {
		return $this->getService('router')->generate($route, $parameters);
	}

	/**
	 * @param Crawler $crawler
	 * @return string
	 */
	protected function getHtml(Crawler $crawler) {
		$html = '';

		foreach ($crawler as $domElement) {
			$html .= $domElement->ownerDocument->saveHTML();
		}

		return $html;
	}

	/**
	 * @param int|string $expectedStepNumber
	 * @param Crawler $crawler
	 */
	protected function assertCurrentStepNumber($expectedStepNumber, Crawler $crawler) {
		$this->assertEquals($expectedStepNumber, $this->getNodeText('#step-number', $crawler));
	}

	/**
	 * @param string $expectedJson
	 * @param Crawler $crawler
	 */
	protected function assertCurrentFormData($expectedJson, Crawler $crawler) {
		$this->assertEquals($expectedJson, $this->getNodeText('#form-data', $crawler));
	}

	/**
	 * @param string $expectedSrcAttr
	 * @param Crawler $crawler
	 */
	protected function assertRenderedImageUrl($expectedSrcAttr, Crawler $crawler) {
		$this->assertEquals($expectedSrcAttr, $this->getNodeAttribute('#rendered-image', 'src', $crawler));
	}

	/**
	 * @param string $expectedError
	 * @param Crawler $crawler
	 */
	protected function assertContainsFormError($expectedError, Crawler $crawler) {
		$this->assertContains($expectedError, $this->getNodeText('form', $crawler));
	}

	/**
	 * @param string $unexpectedError
	 * @param Crawler $crawler
	 */
	protected function assertNotContainsFormError($unexpectedError, Crawler $crawler) {
		$this->assertNotContains($unexpectedError, $this->getNodeText('form', $crawler));
	}

	/**
	 * @param string $expectedJson
	 */
	protected function assertJsonResponse($expectedJson) {
		$this->assertEquals('application/json', static::$client->getResponse()->headers->get('Content-Type') );
		$this->assertEquals($expectedJson, static::$client->getResponse()->getContent());
	}

	/**
	 * @param string $selector
	 * @param string $attribute
	 * @param Crawler $crawler
	 */
	private function getNodeAttribute($selector, $attribute, Crawler $crawler) {
		try {
			return $crawler->filter($selector)->attr($attribute);
		} catch (\InvalidArgumentException $e) {
			$this->fail(sprintf("No node found for selector '%s'. Content:\n%s", $selector, static::$client->getResponse()->getContent()));
		}
	}

	/**
	 * @param string $selector
	 * @param Crawler $crawler
	 */
	private function getNodeText($selector, Crawler $crawler) {
		try {
			return $crawler->filter($selector)->text(null, true);
		} catch (\InvalidArgumentException $e) {
			$this->fail(sprintf("No node found for selector '%s'. Content:\n%s", $selector, static::$client->getResponse()->getContent()));
		}
	}

}
