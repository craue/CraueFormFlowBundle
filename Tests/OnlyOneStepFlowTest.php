<?php

namespace Craue\FormFlowBundle\Tests\Flow;

use Craue\FormFlowBundle\Tests\IntegrationTestCase;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class OnlyOneStepFlowTest extends IntegrationTestCase {

	public function testFlowWithOnlyOneStep() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_onlyOneStep'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);

		// finish flow
		$form = $crawler->selectButton('finish')->form();
		$this->client->submit($form);
		$this->assertJsonResponse('{}');
	}

}
