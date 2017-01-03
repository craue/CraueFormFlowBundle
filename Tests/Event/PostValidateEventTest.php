<?php

namespace Craue\FormFlowBundle\Tests\Event;

use Craue\FormFlowBundle\Event\PostValidateEvent;
use Craue\FormFlowBundle\Tests\UnitTestCase;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PostValidateEventTest extends UnitTestCase {

	public function testEvent() {
		$formData = array('blah' => '123');

		$event = new PostValidateEvent($this->getMockedFlowInterface(), $formData);

		$this->assertEquals($formData, $event->getFormData());
	}

}
