<?php

namespace Craue\FormFlowBundle\Tests;

use Craue\FormFlowBundle\Tests\IntegrationTestCase;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CreateTopicFlowTest extends IntegrationTestCase {

	public function testCreateTopic_dynamicStepNavigation() {
		$this->client->followRedirects();
		$crawler = $this->client->request('GET', $this->url('_FormFlow_createTopic_start'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"title":null,"description":null,"category":null,"comment":null,"details":null}', $crawler);
		$this->assertCount(0, $crawler->filter('#step-list a'));

		// bug report -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createTopic[title]' => 'blah',
			'createTopic[category]' => 'BUG_REPORT',
		));
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":null,"details":null}', $crawler);
		$this->assertCount(1, $crawler->filter('#step-list a'));

		// comment -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createTopic[comment]' => 'my comment',
		));
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":"my comment","details":null}', $crawler);
		$this->assertCount(2, $crawler->filter('#step-list a'));

		// empty bug details -> step 3 again
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createTopic[details]' => '',
		));
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertContainsFormError('This value should not be blank.', $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":"my comment","details":null}', $crawler);

		// bug details -> step 4
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createTopic[details]' => 'blah blah',
		));
		$this->assertCurrentStepNumber(4, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":"my comment","details":"blah blah"}', $crawler);
		$this->assertCount(3, $crawler->filter('#step-list a'));

		// back to step 1 via DSN
		$linkToStep1 = $crawler->filter('#step-list a')->selectLink('basics')->link()->getUri();
		$crawler = $this->client->request('GET', $linkToStep1);
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":"my comment","details":"blah blah"}', $crawler);
		$this->assertCount(3, $crawler->filter('#step-list a'));

		// discussion -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createTopic[title]' => 'blah',
			'createTopic[category]' => 'DISCUSSION',
		));
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"DISCUSSION","comment":"my comment","details":"blah blah"}', $crawler);
		$this->assertCount(2, $crawler->filter('#step-list a')); // link the last step as it's been visited already

		// keep as is -> step 4
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber(4, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"DISCUSSION","comment":"my comment","details":null}', $crawler);
		$this->assertCount(2, $crawler->filter('#step-list a'));

		// finish flow
		$form = $crawler->selectButton('finish')->form();
		$this->client->submit($form);
		$this->assertJsonResponse('{"title":"blah","description":null,"category":"DISCUSSION","comment":"my comment","details":null}');

// var_dump($this->client->getResponse()->getContent());
// die;
	}

	public function testCreateTopic_dynamicStepNavigation2() {
		$this->client->followRedirects();
		$crawler = $this->client->request('GET', $this->url('_FormFlow_createTopic_start'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCount(0, $crawler->filter('#step-list a'));

		// discussion -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createTopic[title]' => 'blah',
			'createTopic[category]' => 'DISCUSSION',
		));
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCount(1, $crawler->filter('#step-list a')); // don't link the last step as it's not been visited yet

		// keep as is -> step 4
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber(4, $crawler);
		$this->assertCount(2, $crawler->filter('#step-list a'));

// var_dump($this->client->getResponse()->getContent());
// die;
	}

	public function testCreateTopic_preserveDataOnGetRequest() {
		$this->client->followRedirects();
		$crawler = $this->client->request('GET', $this->url('_FormFlow_createTopic_start'));

		// discussion -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form, array(
			'createTopic[title]' => 'blah',
			'createTopic[category]' => 'DISCUSSION',
		));

		// GET request -> step 1 with data preserved
		$crawler = $this->client->request('GET', $this->url('_FormFlow_createTopic'));
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"DISCUSSION","comment":null,"details":null}', $crawler);
	}

}
