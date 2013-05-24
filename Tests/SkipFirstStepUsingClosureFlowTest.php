<?php

namespace Craue\FormFlowBundle\Tests\Flow;

use Craue\FormFlowBundle\Tests\IntegrationTestCase;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2013 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class SkipFirstStepUsingClosureFlowTest extends IntegrationTestCase {

	public function testSkipFirstStepUsingClosure() {
		$crawler = $this->client->request('GET', $this->url('_FormFlow_skipFirstStepUsingClosure'));
		$this->assertSame(200, $this->client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(2, $crawler);
	}

}
