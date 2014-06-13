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
class SkipFirstStepUsingClosureFlowTest extends IntegrationTestCase {

	public function testSkipFirstStepUsingClosure() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_skipFirstStepUsingClosure'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(2, $crawler);
		// step 1 must be marked as skipped
		$this->assertContains('<li class="craue_formflow_skipped_step">step1</li>', $this->getHtml($crawler->filter('#step-list')));

		// reset
		$form = $crawler->selectButton('start over')->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber(2, $crawler);
		// step 1 must be marked as skipped
		$this->assertContains('<li class="craue_formflow_skipped_step">step1</li>', $this->getHtml($crawler->filter('#step-list')));
	}

}
