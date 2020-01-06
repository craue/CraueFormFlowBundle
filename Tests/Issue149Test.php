<?php

namespace Craue\FormFlowBundle\Tests;

/**
 * @group integration
 * @see https://github.com/craue/CraueFormFlowBundle/issues/149
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue149Test extends IntegrationTestCase {

	/**
	 * The issue is caused by existence of a file field, regardless of actually uploading a file.
	 */
	public function testIssue149() {
		$crawler = static::$client->request('GET', $this->url('_FormFlow_issue149'));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"photo":null}', $crawler);

		// enter a title -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'issue149[photo][title]' => 'blue pixel',
		]);
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{"photo":{"image":null,"title":"blue pixel"}}', $crawler);

		// next -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(3, $crawler);
		// ensure that the title is preserved
		$this->assertCurrentFormData('{"photo":{"image":null,"title":"blue pixel"}}', $crawler);
	}

}
