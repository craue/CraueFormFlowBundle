<?php

namespace Craue\FormFlowBundle\Tests;

/**
 * @group integration
 * @see https://github.com/craue/CraueFormFlowBundle/issues/64
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue64Test extends IntegrationTestCase {

	public function testIssue64() {
		$crawler = static::$client->request('GET', $this->url('_FormFlow_issue64'));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"sub":null}', $crawler);

		// set prop1 -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'issue64[sub][prop1]' => 'foo',
		]);
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{"sub":{"prop1":"foo","prop2":null}}', $crawler);

		// set prop2 -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'issue64[sub][prop2]' => 'bar',
		]);
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertCurrentFormData('{"sub":{"prop1":"foo","prop2":"bar"}}', $crawler);

		// set different prop2 -> step 4
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'issue64[sub][prop2]' => 'baz',
		]);
		$this->assertCurrentStepNumber(4, $crawler);
		$this->assertCurrentFormData('{"sub":{"prop1":"foo","prop2":"baz"}}', $crawler);

		// finish flow
		$form = $crawler->selectButton('finish')->form();
		static::$client->submit($form);
		$this->assertJsonResponse('{"sub":{"prop1":"foo","prop2":"baz"}}');
	}

}
