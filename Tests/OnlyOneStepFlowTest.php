<?php

namespace Craue\FormFlowBundle\Tests;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class OnlyOneStepFlowTest extends IntegrationTestCase {

	public function testFlowWithOnlyOneStep() {
		$crawler = static::$client->request('GET', $this->url('_FormFlow_onlyOneStep'));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);

		// finish flow
		$form = $crawler->selectButton('finish')->form();
		static::$client->submit($form);
		$this->assertJsonResponse('{}');
	}

}
