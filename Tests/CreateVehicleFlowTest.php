<?php

namespace Craue\FormFlowBundle\Tests;

use Craue\FormFlowBundle\Tests\IntegrationTestCase;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
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

	public function testCreateVehicle_tamperWithHiddenField() {
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
		if (version_compare(Kernel::VERSION, '2.4', '>=')) {
			$form->disableValidation();
			$crawler = $this->client->submit($form, array(
				'createVehicle[numberOfWheels]' => 99,
			));
		} else {
			// impossible to send invalid values with DomCrawler, see https://github.com/symfony/symfony/issues/7672
			// TODO remove as soon as Symfony >= 2.4 is required
			$crawler = $this->client->request($form->getMethod(), $form->getUri(), array(
				'flow_createVehicle_step' => 1,
				'createVehicle' => array(
					'numberOfWheels' => 99,
				),
			));
		}
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
		if (version_compare(Kernel::VERSION, '2.4', '>=')) {
			$form->disableValidation();
			$crawler = $this->client->submit($form, array(
				'createVehicle[engine]' => 'magic',
			));
		} else {
			// impossible to send invalid values with DomCrawler, see https://github.com/symfony/symfony/issues/7672
			// TODO remove as soon as Symfony >= 2.4 is required
			$crawler = $this->client->request($form->getMethod(), $form->getUri(), array(
				'flow_createVehicle_step' => 2,
				'createVehicle' => array(
					'engine' => 'magic',
				),
			));
		}
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertContainsFormError('This value is not valid.', $crawler);
	}

}
