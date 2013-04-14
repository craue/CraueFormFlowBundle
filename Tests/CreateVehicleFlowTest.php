<?php

namespace Craue\FormFlowBundle\Tests\Flow;

use Craue\FormFlowBundle\Tests\IntegrationTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2013 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class CreateVehicleFlowTest extends IntegrationTestCase {

	public function testCreateVehicle_skipAndBack() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_createVehicle'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"numberOfWheels":null,"engine":null}', $crawler);

		// 2 wheels -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createVehicle[numberOfWheels]' => 2,
		));
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertCurrentFormData('{"numberOfWheels":2,"engine":null}', $crawler);

		// go back
		$form = $crawler->selectButton('back')->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"numberOfWheels":2,"engine":null}', $crawler);

		// 4 wheels -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createVehicle[numberOfWheels]' => 4,
		));
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{"numberOfWheels":4,"engine":null}', $crawler);

		// any engine -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createVehicle[engine]' => 'gas',
		));
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertCurrentFormData('{"numberOfWheels":4,"engine":"gas"}', $crawler);

		// finish flow
		$form = $crawler->selectButton('finish')->form();
		$this->client->submit($form);
		$this->assertJsonResponse('{"numberOfWheels":4,"engine":"gas"}');

// var_dump($this->client->getResponse()->getContent());
// die;
	}

	public function testCreateVehicle_invalidateStepData() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_createVehicle'));

		// 4 wheels -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createVehicle[numberOfWheels]' => 4,
		));
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{"numberOfWheels":4,"engine":null}', $crawler);

		// any engine -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createVehicle[engine]' => 'gas',
		));
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertCurrentFormData('{"numberOfWheels":4,"engine":"gas"}', $crawler);

		// go back
		$form = $crawler->selectButton('back')->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{"numberOfWheels":4,"engine":"gas"}', $crawler);

		// go back
		$form = $crawler->selectButton('back')->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"numberOfWheels":4,"engine":"gas"}', $crawler);

		// 2 wheels -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createVehicle[numberOfWheels]' => 2,
		));
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertCurrentFormData('{"numberOfWheels":2,"engine":null}', $crawler);

		// finish flow
		$form = $crawler->selectButton('finish')->form();
		$this->client->submit($form);
		$this->assertJsonResponse('{"numberOfWheels":2,"engine":null}');
	}

	public function testCreateVehicle_reset() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_createVehicle'));

		// 4 wheels -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createVehicle[numberOfWheels]' => 4,
		));
		$this->assertCurrentStepNumber(2, $crawler);

		// any engine -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createVehicle[engine]' => 'gas',
		));
		$this->assertCurrentStepNumber(3, $crawler);

		// reset
		$form = $crawler->selectButton('start over')->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"numberOfWheels":null,"engine":null}', $crawler);

		// 2 wheels -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createVehicle[numberOfWheels]' => 2,
		));
		$this->assertCurrentStepNumber(3, $crawler);

		// finish flow
		$form = $crawler->selectButton('finish')->form();
		$this->client->submit($form);
		$this->assertJsonResponse('{"numberOfWheels":2,"engine":null}');
	}

}
