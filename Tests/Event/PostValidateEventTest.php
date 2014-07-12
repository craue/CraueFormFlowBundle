<?php

namespace Craue\FormFlowBundle\Tests\Event;

use Craue\FormFlowBundle\Event\PostValidateEvent;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PostValidateEventTest extends \PHPUnit_Framework_TestCase {

	public function testEvent() {
		$formData = array('blah' => '123');

		$event = new PostValidateEvent($this->getMockForAbstractClass('\Craue\FormFlowBundle\Form\FormFlow'), $formData);

		$this->assertEquals($formData, $event->getFormData());
	}

}
