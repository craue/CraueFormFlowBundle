<?php

namespace Craue\FormFlowBundle\Tests;

use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\RevalidatePreviousStepsData;
use Craue\FormFlowBundle\Tests\IntegrationTestCase;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
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

	public function testRevalidatePreviousSteps_enabled() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_revalidatePreviousSteps_enabled'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());

		// next -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form);

		// trying to go to step 3, but validation changed for step 1
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertContainsFormError('The form for step 1 is invalid. Please go back and try to submit it again.', $crawler);

		// back -> step 1
		$form = $crawler->selectButton('back')->form();
		$crawler = $this->client->submit($form);

		// trying to go to step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertContainsFormError('Take this!', $crawler);

// var_dump($this->client->getResponse()->getContent());
// die;
	}

	public function testRevalidatePreviousSteps_disabled() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_revalidatePreviousSteps_disabled'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());

		// next -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form);

		// next -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber(3, $crawler);

// var_dump($this->client->getResponse()->getContent());
// die;
	}

}
