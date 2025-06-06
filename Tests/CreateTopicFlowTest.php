<?php

namespace Craue\FormFlowBundle\Tests;

use Symfony\Component\DomCrawler\Crawler;

/**
 * @group integration
 * @group run-with-multiple-databases
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2025 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CreateTopicFlowTest extends IntegrationTestCase {

	public function testCreateTopic_dynamicStepNavigation() {
		static::$client->followRedirects();
		$crawler = static::$client->request('GET', $this->url('_FormFlow_createTopic'));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"title":null,"description":null,"category":null,"comment":null,"details":null}', $crawler);
		$this->assertCount(0, $crawler->filter('#step-list a'));

		// bug report -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[title]' => 'blah',
			'createTopic[category]' => 'BUG_REPORT',
		]);
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":null,"details":null}', $crawler);
		$this->assertCount(1, $crawler->filter('#step-list a'));

		// comment -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[comment]' => 'my comment',
		]);
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":"my comment","details":null}', $crawler);
		$this->assertCount(2, $crawler->filter('#step-list a'));

		// empty bug details -> step 3 again
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[details]' => '',
		]);
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertContainsFormError('This value should not be blank.', $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":"my comment","details":null}', $crawler);

		// bug details -> step 4
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[details]' => 'blah blah',
		]);
		$this->assertCurrentStepNumber(4, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":"my comment","details":"blah blah"}', $crawler);
		$this->assertCount(3, $crawler->filter('#step-list a'));

		// back to step 1 via DSN
		$linkToStep1 = $crawler->filter('#step-list a')->selectLink('basics')->link()->getUri();
		$crawler = static::$client->request('GET', $linkToStep1);
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":"my comment","details":"blah blah"}', $crawler);
		$this->assertCount(3, $crawler->filter('#step-list a'));

		// discussion -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[title]' => 'blah',
			'createTopic[category]' => 'DISCUSSION',
		]);
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"DISCUSSION","comment":"my comment","details":"blah blah"}', $crawler);
		$this->assertCount(2, $crawler->filter('#step-list a')); // link the last step as it's been visited already

		// keep as is -> step 4
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(4, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"DISCUSSION","comment":"my comment","details":null}', $crawler);
		$this->assertCount(2, $crawler->filter('#step-list a'));

		// finish flow
		$form = $crawler->selectButton('finish')->form();
		static::$client->submit($form);
		$this->assertJsonResponse('{"title":"blah","description":null,"category":"DISCUSSION","comment":"my comment","details":null}');
	}

	public function testCreateTopic_dynamicStepNavigation_noLinkForNonVisitedStep() {
		static::$client->followRedirects();
		$crawler = static::$client->request('GET', $this->url('_FormFlow_createTopic'));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCount(0, $crawler->filter('#step-list a'));

		// discussion -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[title]' => 'blah',
			'createTopic[category]' => 'DISCUSSION',
		]);
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCount(1, $crawler->filter('#step-list a')); // don't link the last step as it's not been visited yet

		// keep as is -> step 4
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(4, $crawler);
		$this->assertCount(2, $crawler->filter('#step-list a'));
	}

	public function testCreateTopic_dynamicStepNavigation_preserveDataOnGetRequestWithInstanceId() {
		static::$client->followRedirects();
		$crawler = static::$client->request('GET', $this->url('_FormFlow_createTopic'));

		// discussion -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[title]' => 'blah',
			'createTopic[category]' => 'DISCUSSION',
		]);

		// GET request -> step 1 with data preserved
		$crawler = static::$client->request('GET', $this->url('_FormFlow_createTopic', [
			'instance' => $form->get('flow_createTopic_instance')->getValue(),
		]));
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"DISCUSSION","comment":null,"details":null}', $crawler);
	}

	public function testCreateTopic_dynamicStepNavigation_newFlowInstanceOnGetRequest() {
		static::$client->followRedirects();
		$crawler = static::$client->request('GET', $this->url('_FormFlow_createTopic'));

		// discussion -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[title]' => 'blah',
			'createTopic[category]' => 'DISCUSSION',
		]);

		// GET request -> step 1 with new flow instance
		$crawler = static::$client->request('GET', $this->url('_FormFlow_createTopic'));
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"title":null,"description":null,"category":null,"comment":null,"details":null}', $crawler);
	}

	public function testCreateTopic_redirectAfterSubmit() {
		static::$client->followRedirects();
		$crawler = static::$client->request('GET', $this->url('_FormFlow_createTopic_redirectAfterSubmit'));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"title":null,"description":null,"category":null,"comment":null,"details":null}', $crawler);
		$this->assertCount(0, $crawler->filter('#step-list a'));

		// reset -> step 1
		$form = $crawler->selectButton('start over')->form();
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"title":null,"description":null,"category":null,"comment":null,"details":null}', $crawler);
		$this->assertCount(0, $crawler->filter('#step-list a'));
		// make sure redirection was effective after clicking "start over"
		$this->assertEquals('GET', static::$client->getRequest()->getMethod());
		$this->assertArrayHasKey('instance', static::$client->getRequest()->query->all());
		$this->assertEquals(1, static::$client->getRequest()->query->get('step'));

		// empty title -> step 1 again
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[title]' => '',
		]);
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertContainsFormError('This value should not be blank.', $crawler);
		$this->assertCurrentFormData('{"title":null,"description":null,"category":null,"comment":null,"details":null}', $crawler);
		// make sure query parameters are still added in case of form errors
		$this->assertEquals('POST', static::$client->getRequest()->getMethod());
		$this->assertArrayHasKey('instance', static::$client->getRequest()->query->all());
		$this->assertEquals(1, static::$client->getRequest()->query->get('step'));

		// bug report -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[title]' => 'blah',
			'createTopic[category]' => 'BUG_REPORT',
		]);
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":null,"details":null}', $crawler);
		$this->assertCount(0, $crawler->filter('#step-list a'));
		// make sure redirection was effective after clicking "next"
		$this->assertEquals('GET', static::$client->getRequest()->getMethod());
		$this->assertArrayHasKey('instance', static::$client->getRequest()->query->all());
		$this->assertEquals(2, static::$client->getRequest()->query->get('step'));

		// comment -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[comment]' => 'my comment',
		]);
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":"my comment","details":null}', $crawler);
		$this->assertCount(0, $crawler->filter('#step-list a'));

		// empty bug details -> step 3 again
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[details]' => '',
		]);
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertContainsFormError('This value should not be blank.', $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":"my comment","details":null}', $crawler);

		// bug details -> step 4
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[details]' => 'blah blah',
		]);
		$this->assertCurrentStepNumber(4, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":"my comment","details":"blah blah"}', $crawler);
		$this->assertCount(0, $crawler->filter('#step-list a'));

		// back -> step 3
		$form = $crawler->selectButton('back')->form();
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":"my comment","details":"blah blah"}', $crawler);
		$this->assertCount(0, $crawler->filter('#step-list a'));
		// make sure redirection was effective after clicking "back"
		$this->assertEquals('GET', static::$client->getRequest()->getMethod());
		$this->assertArrayHasKey('instance', static::$client->getRequest()->query->all());
		$this->assertEquals(3, static::$client->getRequest()->query->get('step'));

		// next -> step 4
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(4, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":"my comment","details":"blah blah"}', $crawler);
		$this->assertCount(0, $crawler->filter('#step-list a'));

		// finish flow
		$form = $crawler->selectButton('finish')->form();
		static::$client->submit($form);
		$this->assertJsonResponse('{"title":"blah","description":null,"category":"BUG_REPORT","comment":"my comment","details":"blah blah"}');
	}

	public function testCreateTopic_dynamicStepNavigation_invalidInstanceId_onGetRequest() {
		$crawler = $this->proceedToStep(2);

		$fakeInstanceId = 'invalid instance id';

		$crawler = static::$client->request('GET', $this->url('_FormFlow_createTopic', [
			'instance' => $fakeInstanceId,
			'step' => 2,
		]));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);

		$this->assertNotEquals($fakeInstanceId, $crawler->selectButton('next')->form()->get('flow_createTopic_instance')->getValue());
	}

	public function testCreateTopic_dynamicStepNavigation_invalidInstanceId_onPostRequest() {
		$crawler = $this->proceedToStep(2);

		$fakeInstanceId = 'invalid instance id';

		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'flow_createTopic_instance' => $fakeInstanceId,
		]);
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);

		$this->assertNotEquals($fakeInstanceId, $crawler->selectButton('next')->form()->get('flow_createTopic_instance')->getValue());
	}

	public function testCreateTopic_dynamicStepNavigation_invalidStep_exceedLowerLimit() {
		$crawler = $this->proceedToStep(2);

		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->request('GET', $this->url('_FormFlow_createTopic', [
			'instance' => $form->get('flow_createTopic_instance')->getValue(),
			'step' => 0,
		]));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);
	}

	public function testCreateTopic_dynamicStepNavigation_invalidStep_exceedUpperLimit() {
		$crawler = $this->proceedToStep(4);

		$form = $crawler->selectButton('finish')->form();
		$crawler = static::$client->request('GET', $this->url('_FormFlow_createTopic', [
			'instance' => $form->get('flow_createTopic_instance')->getValue(),
			'step' => 5,
		]));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(4, $crawler);
	}

	public function testCreateTopic_dynamicStepNavigation_invalidStep_noInteger() {
		$crawler = $this->proceedToStep(2);

		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->request('GET', $this->url('_FormFlow_createTopic', [
			'instance' => $form->get('flow_createTopic_instance')->getValue(),
			'step' => 'x',
		]));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);
	}

	/**
	 * Processes through the flow up to the given step by filling out the forms with some valid data.
	 * @param int $stepNumber The targeted step number.
	 * @return Crawler
	 */
	private function proceedToStep($stepNumber) {
		static::$client->followRedirects();
		$crawler = static::$client->request('GET', $this->url('_FormFlow_createTopic'));

		if ($stepNumber < 2) {
			return $crawler;
		}

		// bug report -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[title]' => 'blah',
			'createTopic[category]' => 'BUG_REPORT',
		]);

		if ($stepNumber < 3) {
			return $crawler;
		}

		// comment -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[comment]' => 'my comment',
		]);

		if ($stepNumber < 4) {
			return $crawler;
		}

		// bug details -> step 4
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[details]' => 'blah blah',
		]);

		return $crawler;
	}

}
