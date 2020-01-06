<?php

namespace Craue\FormFlowBundle\Tests;

/**
 * @group integration
 * @see https://github.com/craue/CraueFormFlowBundle/issues/249
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class UsualFormTest extends IntegrationTestCase {

	/**
	 * Ensure that there's no error about the flow being expired when a 2nd (usual) form is submitted instead of the flow's form.
	 */
	public function testIssue249_submitUsualForm_flowNotExpired() {
		$crawler = static::$client->request('GET', $this->url('_FormFlow_usualForm'));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"title":null,"description":null,"category":null,"comment":null,"details":null}', $crawler);

		// bug report -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'createTopic[title]' => 'blah',
			'createTopic[category]' => 'BUG_REPORT',
		]);
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{"title":"blah","description":null,"category":"BUG_REPORT","comment":null,"details":null}', $crawler);

		// submit usual form
		$form = $crawler->selectButton('submit usual form')->form();
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"title":null,"description":null,"category":null,"comment":null,"details":null}', $crawler);
		$this->assertNotContainsFormError('This form has expired. Please submit it again.', $crawler);
	}

}
