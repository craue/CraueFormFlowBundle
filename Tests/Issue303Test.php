<?php

namespace Craue\FormFlowBundle\Tests;

use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\Issue303Flow;
use Craue\FormFlowBundle\Tests\IntegrationTestCase;

/**
 * @group integration
 * @see https://github.com/craue/CraueFormFlowBundle/issues/303
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2018 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue303Test extends IntegrationTestCase {

	/**
	 * @dataProvider dataIssue303
	 */
	public function testIssue303($stepNumberToSkipInBetween, $buttonToPressInBetween, $expectedTargetStep) {
		Issue303Flow::resetSkips();

		$crawler = $this->client->request('GET', $this->url('_FormFlow_issue303'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());

		// next -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form);

		Issue303Flow::setSkip($stepNumberToSkipInBetween);

		// press button -> target step
		$form = $crawler->selectButton($buttonToPressInBetween)->form();
		$crawler = $this->client->submit($form);
		$this->assertCurrentStepNumber($expectedTargetStep, $crawler);
	}

	public function dataIssue303() {
		return array(
			array(2, 'start over', 1),
			array(1, 'back', 2),

			array(1, 'start over', 2),
			array(2, 'back', 1),
		);
	}

}
