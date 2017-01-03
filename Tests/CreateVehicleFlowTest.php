<?php

namespace Craue\FormFlowBundle\Tests;

use Craue\FormFlowBundle\Tests\IntegrationTestCase;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
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

		// go back -> step 1
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
	}

	public function testCreateVehicle_flowExpired() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_createVehicle'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);

		// 2 wheels -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createVehicle[numberOfWheels]' => 2,
		));
		$this->assertCurrentStepNumber(3, $crawler);

		// finish flow
		$expiredCrawler = $crawler;
		$form = $crawler->selectButton('finish')->form();
		$this->client->submit($form);

		// try submitting an expired form again and do it multiple times to ensure it's not reinitialized
		for ($i = 0; $i < 3; ++$i) {
			$form = $expiredCrawler->selectButton('finish')->form();
			$oldInstanceId = $form->get('flow_createVehicle_instance')->getValue();
			$crawler = $this->client->submit($form);
			$this->assertContainsFormError('This form has expired. Please submit it again.', $crawler);

			// ensure the instance id is different
			$newForm = $crawler->selectButton('next')->form();
			$this->assertNotEquals($oldInstanceId, $newForm->get('flow_createVehicle_instance')->getValue());
		}
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

	public function testCreateVehicle_reload() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_createVehicle'));

		// 2 wheels -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createVehicle[numberOfWheels]' => 2,
		));
		$this->assertCurrentStepNumber(3, $crawler);

		// reload -> step 3 again
		$this->client->reload();
		$this->assertCurrentStepNumber(3, $crawler);
	}

	public function testCreateVehicle_resetFlowOnGetRequest() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_createVehicle'));

		// 2 wheels -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createVehicle[numberOfWheels]' => 2,
		));

		// GET request -> step 1 with clean data
		$crawler = $this->client->request('GET', $this->url('_FormFlow_createVehicle'));
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"numberOfWheels":null,"engine":null}', $crawler);
	}

	public function testCreateVehicle_tamperWithHiddenStepField() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_createVehicle'));

		// no number of wheels -> step 1 again
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'flow_createVehicle_step' => '3',
		));
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertContainsFormError('This value should not be blank.', $crawler);
		$this->assertCurrentFormData('{"numberOfWheels":null,"engine":null}', $crawler);

		// 4 wheels -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'flow_createVehicle_step' => '3',
			'createVehicle[numberOfWheels]' => 4,
		));
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{"numberOfWheels":4,"engine":null}', $crawler);
	}

	public function testCreateVehicle_unskipStepWhenGoingBack() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_createVehicle'));

		// 2 wheels -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createVehicle[numberOfWheels]' => 2,
		));
		$this->assertCurrentStepNumber(3, $crawler);
		// step 2 must be marked as skipped
		$this->assertContains('<li class="craue_formflow_skipped_step">engine</li>', $this->getHtml($crawler->filter('#step-list')));

		// go back
		$form = $crawler->selectButton('back')->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber(1, $crawler);
		// step 2 must not be marked as skipped
		$this->assertContains('<li>engine</li>', $this->getHtml($crawler->filter('#step-list')));
	}

	public function testCreateVehicle_submitInvalidValues() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_createVehicle'));

		// invalid number of wheels -> step 1 again
		$form = $crawler->selectButton('next')->form();
		$form->disableValidation();
		$crawler = $this->client->submit($form, array(
			'createVehicle[numberOfWheels]' => 99,
		));
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertContainsFormError('This value is not valid.', $crawler);

		// 4 wheels -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createVehicle[numberOfWheels]' => 4,
		));
		$this->assertCurrentStepNumber(2, $crawler);

		// invalid engine -> step 2 again
		$form = $crawler->selectButton('next')->form();
		$form->disableValidation();
		$crawler = $this->client->submit($form, array(
			'createVehicle[engine]' => 'magic',
		));
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertContainsFormError('This value is not valid.', $crawler);
	}

}
