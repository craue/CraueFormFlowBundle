<?php

namespace Craue\FormFlowBundle\Tests;

use Craue\FormFlowBundle\Tests\IntegrationTestCase;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class AdditionalValidationGroupsPerStepFlowTest extends IntegrationTestCase {

	public function testAdditionalValidationGroupsPerStep() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_additionalValidationGroupsPerStep'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);

		// blank value -> step 1
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'additionalValidationGroupsPerStep[value]' => '',
		));
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertContainsFormError('This value should not be blank.', $crawler);

		// valid value -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'additionalValidationGroupsPerStep[value]' => 'blah',
		));
		$this->assertCurrentStepNumber(2, $crawler);

		// blank value -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'additionalValidationGroupsPerStep[value]' => '',
		));
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertContainsFormError('This value should not be blank.', $crawler);

		// valid value -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'additionalValidationGroupsPerStep[value]' => 'blah',
		));
		$this->assertCurrentStepNumber(3, $crawler);

		// blank value -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'additionalValidationGroupsPerStep[value]' => '',
		));
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertContainsFormError('This value should not be blank.', $crawler);

		// invalid value -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'additionalValidationGroupsPerStep[value]' => '123',
		));
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertContainsFormError('This value is not valid.', $crawler);

		// valid value -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'additionalValidationGroupsPerStep[value]' => 'blah',
		));
		$this->assertCurrentStepNumber(4, $crawler);
	}

}
