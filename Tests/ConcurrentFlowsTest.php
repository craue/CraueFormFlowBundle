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
class ConcurrentFlowsTest extends IntegrationTestCase {

	public function testCreateTopic_concurrentUsageOfTwoFlows() {
		$this->client->followRedirects();

		// [A] start flow
		$crawlerA = $this->client->request('GET', $this->url('_FormFlow_createTopic'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawlerA);
		$this->assertCurrentFormData('{"title":null,"description":null,"category":null,"comment":null,"details":null}', $crawlerA);

		// [B] start flow
		$crawlerB = $this->client->request('GET', $this->url('_FormFlow_createTopic'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawlerB);
		$this->assertCurrentFormData('{"title":null,"description":null,"category":null,"comment":null,"details":null}', $crawlerB);

		// [A] bug report -> step 2
		$formA = $crawlerA->selectButton('next')->form();
		$crawlerA = $this->client->submit($formA, array(
			'createTopic[title]' => 'error',
			'createTopic[category]' => 'BUG_REPORT',
		));
		$this->assertCurrentStepNumber(2, $crawlerA);
		$this->assertCurrentFormData('{"title":"error","description":null,"category":"BUG_REPORT","comment":null,"details":null}', $crawlerA);

		// [B] discussion -> step 2
		$formB = $crawlerB->selectButton('next')->form();
		$crawlerB = $this->client->submit($formB, array(
			'createTopic[title]' => 'question',
			'createTopic[category]' => 'DISCUSSION',
		));
		$this->assertCurrentStepNumber(2, $crawlerB);
		$this->assertCurrentFormData('{"title":"question","description":null,"category":"DISCUSSION","comment":null,"details":null}', $crawlerB);

		// [A] comment -> step 3
		$formA = $crawlerA->selectButton('next')->form();
		$crawlerA = $this->client->submit($formA, array(
			'createTopic[comment]' => 'my comment',
		));
		$this->assertCurrentStepNumber(3, $crawlerA);
		$this->assertCurrentFormData('{"title":"error","description":null,"category":"BUG_REPORT","comment":"my comment","details":null}', $crawlerA);

		// [A] bug details -> step 4
		$formA = $crawlerA->selectButton('next')->form();
		$crawlerA = $this->client->submit($formA, array(
			'createTopic[details]' => 'blah blah',
		));
		$this->assertCurrentStepNumber(4, $crawlerA);
		$this->assertCurrentFormData('{"title":"error","description":null,"category":"BUG_REPORT","comment":"my comment","details":"blah blah"}', $crawlerA);

		// [B] no comment -> step 4
		$formB = $crawlerB->selectButton('next')->form();
		$crawlerB = $this->client->submit($formB);
		$this->assertCurrentStepNumber(4, $crawlerB);
		$this->assertCurrentFormData('{"title":"question","description":null,"category":"DISCUSSION","comment":null,"details":null}', $crawlerB);

		// [A] finish flow
		$formA = $crawlerA->selectButton('finish')->form();
		$this->client->submit($formA);
		$this->assertJsonResponse('{"title":"error","description":null,"category":"BUG_REPORT","comment":"my comment","details":"blah blah"}');

		// [B] finish flow
		$formB = $crawlerB->selectButton('finish')->form();
		$this->client->submit($formB);
		$this->assertJsonResponse('{"title":"question","description":null,"category":"DISCUSSION","comment":null,"details":null}');
	}

}
