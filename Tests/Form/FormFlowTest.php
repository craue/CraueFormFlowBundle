<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Event\GetStepsEvent;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @group unit
 *
 * @author Toni Uebernickel <tuebernickel@gmail.com>
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2016 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowTest extends \PHPUnit_Framework_TestCase {

	public function testStepListener() {
		$steps = array(
			// TODO replace by `$this->createMock('\Craue\FormFlowBundle\Form\StepInterface')` as soon as PHPUnit >= 5.4 is required
			$this->getMockBuilder('\Craue\FormFlowBundle\Form\StepInterface')->getMock(),
		);

		$dispatcher = new EventDispatcher();
		$dispatcher->addListener(FormFlowEvents::GET_STEPS, function(GetStepsEvent $event) use ($steps) {
			$event->setSteps($steps);

			$event->stopPropagation();
		});

		/* @var $flow \PHPUnit_Framework_MockObject_MockObject|\Craue\FormFlowBundle\Form\FormFlow */
		$flow = $this->getMockForAbstractClass('\Craue\FormFlowBundle\Form\FormFlow');
		$flow->setEventDispatcher($dispatcher);

		$this->assertEquals($steps, $flow->getSteps());
	}

	public function testCreateStepsFromConfig_fixArrayIndexes() {
		$flowStub = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'loadStepsConfig'))->getMock();

		$flowStub
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue(array(
				2 => array(
					'label' => 'step1',
				),
			)))
		;

		$this->assertSame(1, $flowStub->getStep(1)->getNumber());
	}

}
