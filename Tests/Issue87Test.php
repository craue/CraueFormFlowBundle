<?php

namespace Craue\FormFlowBundle\Tests;

use Craue\FormFlowBundle\Tests\IntegrationTestCase;

/**
 * @group integration
 * @see https://github.com/craue/CraueFormFlowBundle/issues/87
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue87Test extends IntegrationTestCase {

	public function testIssue87() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_issue87'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());

		// next -> step 2
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form);

		// next -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = $this->client->submit($form);

		// make sure the DSN link contains the step as a route parameter
		$linkToStep2 = $crawler->filter('#step-list a')->selectLink('step2')->link();
		$this->assertSame('/issue87/2', $linkToStep2->getNode()->attributes->getNamedItem('href')->textContent);

		// back to step 2 via DSN
		$crawler = $this->client->request('GET', $linkToStep2->getUri());
		// make sure we actually arrived at step 2
		$this->assertCurrentStepNumber(2, $crawler);

// var_dump($this->client->getResponse()->getContent());
// die;
	}

}
