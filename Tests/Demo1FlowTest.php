<?php

namespace Craue\FormFlowBundle\Tests;

use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\Demo1Flow;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2019 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Demo1FlowTest extends IntegrationTestCase {

	public function testDemo1_events() {
		$crawler = static::$client->request('GET', $this->url('_FormFlow_demo1'));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{}', $crawler);
		$this->assertCount(0, $crawler->selectButton('back')); // no back button
		$this->assertEquals(['onPreBind', 'onGetSteps', 'onPostBindFlow #2'], $this->getCalledEvents());

		// reset
		$form = $crawler->selectButton('start over')->form();
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertEquals(['onPreBind', 'onGetSteps', 'onPostBindFlow #2'], $this->getCalledEvents());

		// next
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertCount(1, $crawler->selectButton('back')); // back button
		$this->assertCount(0, $crawler->selectButton('finish')); // no finish button
		$this->assertEquals(['onPreBind', 'onGetSteps', 'onPostBindFlow #2', 'onPostBindRequest', 'onPostValidate'], $this->getCalledEvents());

		// next
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(4, $crawler);
		$this->assertCount(1, $crawler->selectButton('finish')); // finish button
		$this->assertEquals(['onPreBind', 'onGetSteps', 'onPostBindSavedData #2', 'onPostBindFlow #3', 'onPostBindRequest', 'onPostValidate'], $this->getCalledEvents());

		// go back
		$form = $crawler->selectButton('back')->form();
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertEquals(['onPreBind', 'onGetSteps', 'onPostBindSavedData #2', 'onPostBindSavedData #3', 'onPostBindFlow #3'], $this->getCalledEvents());

		// next
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(4, $crawler);
		$this->assertEquals(['onPreBind', 'onGetSteps', 'onPostBindSavedData #2', 'onPostBindSavedData #3', 'onPostBindFlow #3', 'onPostBindRequest', 'onPostValidate'], $this->getCalledEvents());

		// finish flow
		$form = $crawler->selectButton('finish')->form();
		static::$client->submit($form);
		$this->assertJsonResponse('{}');
	}

	protected function getCalledEvents() {
		$container = static::$kernel->getContainer();
		$flow = $container->get(Demo1Flow::class);
		$storage = $container->get('craue.form.flow.storage');

		return $storage->get($flow->getCalledEventsSessionKey());
	}

}
