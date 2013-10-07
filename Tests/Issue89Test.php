<?php

namespace Craue\FormFlowBundle\Tests;

use Craue\FormFlowBundle\Tests\IntegrationTestCase;

/**
 * @group integration
 * @see https://github.com/craue/CraueFormFlowBundle/issues/89
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2013 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue89Test extends IntegrationTestCase {

	public function testIssue89() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_issue89'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());

		// change default values -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'issue89[prop1]' => 'new value',
			'issue89[prop2]' => 'new value',
		));
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{"prop1":"new value","prop2":"new value"}', $crawler);

		// go back
		$form = $crawler->selectButton('back')->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber(1, $crawler);

		// next -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber(2, $crawler);
		// changed values should still be there
		$this->assertCurrentFormData('{"prop1":"new value","prop2":"new value"}', $crawler);

// var_dump($this->client->getResponse()->getContent());
// die;
	}

}
