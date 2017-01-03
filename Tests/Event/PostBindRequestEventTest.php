<?php

namespace Craue\FormFlowBundle\Tests\Event;

use Craue\FormFlowBundle\Event\PostBindRequestEvent;
use Craue\FormFlowBundle\Tests\UnitTestCase;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PostBindRequestEventTest extends UnitTestCase {

	public function testEvent() {
		$formData = array('blah' => '123');
		$stepNumber = 2;

		$event = new PostBindRequestEvent($this->getMockedFlowInterface(), $formData, $stepNumber);

		$this->assertEquals($formData, $event->getFormData());
		$this->assertEquals($stepNumber, $event->getStepNumber());
	}

}
