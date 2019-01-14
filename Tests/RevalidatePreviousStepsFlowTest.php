<?php

namespace Craue\FormFlowBundle\Tests;

use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\RevalidatePreviousStepsData;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\RevalidatePreviousStepsFlow;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2019 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class RevalidatePreviousStepsFlowTest extends IntegrationTestCase {

	/**
	 * {@inheritDoc}
	 */
	protected function setUp() {
		parent::setUp();

		RevalidatePreviousStepsData::resetValidationCalls();
	}

	protected function setUpClient() {
	}

	/**
	 * @dataProvider getEnvironmentConfigs
	 */
	public function testRevalidatePreviousSteps_enabled($environment, $config) {
		static::$client = static::createClient(['environment' => $environment, 'config' => $config]);

		$crawler = static::$client->request('GET', $this->url('_FormFlow_revalidatePreviousSteps_enabled'));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());

		// next -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form);

		// trying to go to step 3, but validation changed for step 1
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertContainsFormError('The form for step 1 is invalid. Please go back and try to submit it again.', $crawler);
		$this->assertEquals(['onPreviousStepInvalid #1'], $this->getCalledEvents());

		// back -> step 1
		$form = $crawler->selectButton('back')->form();
		$crawler = static::$client->submit($form);

		// trying to go to step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertContainsFormError('Take this!', $crawler);
	}

	/**
	 * @dataProvider getEnvironmentConfigs
	 */
	public function testRevalidatePreviousSteps_disabled($environment, $config) {
		static::$client = static::createClient(['environment' => $environment, 'config' => $config]);

		$crawler = static::$client->request('GET', $this->url('_FormFlow_revalidatePreviousSteps_disabled'));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());

		// next -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form);

		// next -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(3, $crawler);
	}

	protected function getCalledEvents() {
		$container = static::$kernel->getContainer();
		$flow = $container->get(RevalidatePreviousStepsFlow::class);
		$storage = $container->get('craue.form.flow.storage');

		return $storage->get($flow->getCalledEventsSessionKey());
	}

}
