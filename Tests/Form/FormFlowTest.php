<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Event\GetStepsEvent;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @group unit
 *
 * @author Toni Uebernickel <tuebernickel@gmail.com>
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowTest extends TestCase {

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

	/**
	 * @dataProvider dataApplySkipping
	 */
	public function testApplySkipping($stepCount, array $stepsSkipped, $stepNumber, $direction, $expectedTargetStep) {
		$flow = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'loadStepsConfig'))->getMock();

		$stepsConfig = array();

		for ($stepNumber = 1; $stepNumber <= $stepCount; ++$stepNumber) {
			$stepsConfig[] = array(
				'skip' => in_array($stepNumber, $stepsSkipped, true),
			);
		}

		$flow
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue($stepsConfig))
		;

		$method = new \ReflectionMethod($flow, 'applySkipping');
		$method->setAccessible(true);

		$this->assertSame($expectedTargetStep, $method->invoke($flow, $stepNumber, $direction));
	}

	public function dataApplySkipping() {
		return array(
			array(2, array(2), 2, 1, 1),
			array(2, array(1), 2, -1, 2),

			array(2, array(1), 2, 1, 2),
			array(2, array(2), 2, -1, 1),
		);
	}

}
