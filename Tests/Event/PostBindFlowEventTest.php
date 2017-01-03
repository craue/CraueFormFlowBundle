<?php

namespace Craue\FormFlowBundle\Tests\Event;

use Craue\FormFlowBundle\Event\PostBindFlowEvent;
use Craue\FormFlowBundle\Tests\UnitTestCase;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PostBindFlowEventTest extends UnitTestCase {

	public function testEvent() {
		$formData = array('blah' => '123');

		$event = new PostBindFlowEvent($this->getMockedFlowInterface(), $formData);

		$this->assertEquals($formData, $event->getFormData());
	}

}
