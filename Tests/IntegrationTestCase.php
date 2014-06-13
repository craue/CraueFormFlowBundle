<?php

namespace Craue\FormFlowBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
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
		return new AppKernel(isset($options['config']) ? $options['config'] : 'config.yml');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function setUp() {
		$this->client = static::createClient();
	}

	/**
	 * @param string $route
	 * @param array $parameters
	 * @param boolean $absolute
	 * @return string URL
	 */
	protected function url($route, array $parameters = array(), $absolute = false) {
		return static::$kernel->getContainer()->get('router')->generate($route, $parameters, $absolute);
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
	 * @param integer|string $expectedStepNumber
	 * @param Crawler $crawler
	 */
	protected function assertCurrentStepNumber($expectedStepNumber, Crawler $crawler) {
		$selector = '#step-number';
		try {
			$this->assertEquals($expectedStepNumber, $crawler->filter($selector)->text());
		} catch (\InvalidArgumentException $e) {
			$this->fail(sprintf("No node found for selector '%s'. Content:\n%s", $selector, $this->client->getResponse()->getContent()));
		}
	}

	/**
	 * @param string $expectedJson
	 * @param Crawler $crawler
	 */
	protected function assertCurrentFormData($expectedJson, Crawler $crawler) {
		$selector = '#form-data';
		try {
			$this->assertEquals($expectedJson, $crawler->filter($selector)->text());
		} catch (\InvalidArgumentException $e) {
			$this->fail(sprintf("No node found for selector '%s'. Content:\n%s", $selector, $this->client->getResponse()->getContent()));
		}
	}

	/**
	 * @param string $expectedError
	 * @param Crawler $crawler
	 */
	protected function assertContainsFormError($expectedError, Crawler $crawler) {
		$selector = 'form';
		try {
			$this->assertContains($expectedError, $crawler->filter($selector)->text());
		} catch (\InvalidArgumentException $e) {
			$this->fail(sprintf("No node found for selector '%s'. Content:\n%s", $selector, $this->client->getResponse()->getContent()));
		}
	}

	/**
	 * @param string $expectedJson
	 */
	protected function assertJsonResponse($expectedJson) {
		$this->assertEquals('application/json', $this->client->getResponse()->headers->get('Content-Type') );
		$this->assertEquals($expectedJson, $this->client->getResponse()->getContent());
	}

}
