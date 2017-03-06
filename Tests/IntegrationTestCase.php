<?php

namespace Craue\FormFlowBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class IntegrationTestCase extends WebTestCase {

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * {@inheritDoc}
	 */
	protected static function createKernel(array $options = array()) {
		$configFile = isset($options['config']) ? $options['config'] : 'config.yml';

		return new AppKernel($configFile);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function setUp() {
		$this->client = static::createClient();
	}

	/**
	 * @param string $id The service identifier.
	 * @return object The associated service.
	 */
	protected function getService($id) {
		return static::$kernel->getContainer()->get($id);
	}

	/**
	 * @return \Twig_Environment
	 */
	protected function getTwig() {
		return $this->getService('twig');
	}

	/**
	 * @param string $route
	 * @param array $parameters
	 * @return string URL
	 */
	protected function url($route, array $parameters = array()) {
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
		$this->assertEquals('application/json', $this->client->getResponse()->headers->get('Content-Type') );
		$this->assertEquals($expectedJson, $this->client->getResponse()->getContent());
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
			$this->fail(sprintf("No node found for selector '%s'. Content:\n%s", $selector, $this->client->getResponse()->getContent()));
		}
	}

	/**
	 * @param string $selector
	 * @param Crawler $crawler
	 */
	private function getNodeText($selector, Crawler $crawler) {
		try {
			return $crawler->filter($selector)->text();
		} catch (\InvalidArgumentException $e) {
			$this->fail(sprintf("No node found for selector '%s'. Content:\n%s", $selector, $this->client->getResponse()->getContent()));
		}
	}

}
