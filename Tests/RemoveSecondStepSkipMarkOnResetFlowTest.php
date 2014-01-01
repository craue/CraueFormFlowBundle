<?php

namespace Craue\FormFlowBundle\Tests\Flow;

use Craue\FormFlowBundle\Tests\IntegrationTestCase;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class RemoveSecondStepSkipMarkOnResetFlowTest extends IntegrationTestCase {

	public function testRemoveSecondStepSkipMarkOnReset() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_removeSecondStepSkipMarkOnReset'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{}', $crawler);

		// next
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber(3, $crawler);
		// step 2 must be marked as skipped
		$this->assertContains('<li class="craue_formflow_skipped_step">step2</li>', $this->getHtml($crawler->filter('#step-list')));

		// reset
		$form = $crawler->selectButton('start over')->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber(1, $crawler);
		// step 2 must not be marked as skipped
		$this->assertContains('<li>step2</li>', $this->getHtml($crawler->filter('#step-list')));
	}

}
