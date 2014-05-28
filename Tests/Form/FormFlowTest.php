<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Event\GetStepsEvent;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group unit
 *
 * @author Toni Uebernickel <tuebernickel@gmail.com>
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowTest extends \PHPUnit_Framework_TestCase {

	public function testStepListener() {
		$steps = array(
			$this->getMock('\Craue\FormFlowBundle\Form\StepInterface'),
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
		$flowStub = $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', array('getName', 'loadStepsConfig'));

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
	 * Ensure that the "validation_groups" option can be overridden.
	 */
	public function testGetFormOptions_overrideValidationGroups() {
		$flow = $this->getMockForAbstractClass('\Craue\FormFlowBundle\Form\FormFlow');

		$options = $flow->getFormOptions(1, array(
			'validation_groups' => 'Default',
		));

		$this->assertEquals('Default', $options['validation_groups']);
	}

	/**
	 * Ensure that the generated default value for "validation_groups" is an array, which can be used to just add
	 * other groups.
	 */
	public function testGetFormOptions_generatedValidationGroupIsArray() {
		$flow = $this->getMockForAbstractClass('\Craue\FormFlowBundle\Form\FormFlow');

		$flow
			->expects($this->once())
			->method('getName')
			->will($this->returnValue('createTopic'))
		;

		$options = $flow->getFormOptions(1);

		$this->assertEquals(array('flow_createTopic_step1'), $options['validation_groups']);
	}

	/**
	 * Ensure that generic options are considered.
	 */
	public function testGetFormOptions_considerGenericOptions() {
		$flow = $this->getMockForAbstractClass('\Craue\FormFlowBundle\Form\FormFlow');

		$flow->setGenericFormOptions(array(
			'action' => 'targetUrl',
		));

		$options = $flow->getFormOptions(1);

		$this->assertEquals('targetUrl', $options['action']);
	}

	/**
	 * Ensure that the requested step number is correctly determined.
	 *
	 * @dataProvider dataGetRequestedStepNumber
	 *
	 * @param string $httpMethod The HTTP method.
	 * @param array $parameters Parameters for the query/request.
	 * @param boolean $dsnEnabled If dynamic step navigation is enabled.
	 * @param integer $expectedStepNumber The expected step number being requested.
	 */
	public function testGetRequestedStepNumber($httpMethod, $parameters, $dsnEnabled, $expectedStepNumber) {
		$flow = $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', array('getName', 'getRequest'));

		if ($dsnEnabled) {
			$flow->setAllowDynamicStepNavigation(true);
		}

		$flow
			->expects($this->any())
			->method('getName')
			->will($this->returnValue('createTopic'))
		;

		$flow
			->expects($this->once())
			->method('getRequest')
			->will($this->returnValue(Request::create('', $httpMethod, $parameters)))
		;

		$method = new \ReflectionMethod($flow, 'getRequestedStepNumber');
		$method->setAccessible(true);

		$this->assertSame($expectedStepNumber, $method->invoke($flow));
	}

	public function dataGetRequestedStepNumber() {
		return array(
			array('GET', array(), false, 1),
			array('GET', array(), true, 1),
			array('GET', array('step' => 2), true, 2),
			array('POST', array(), false, 1),
			array('POST', array('flow_createTopic_step' => 2), false, 2),
			array('PUT', array(), false, 1),
			array('PUT', array('flow_createTopic_step' => 2), false, 2),
		);
	}

	/**
	 * Ensure that the form is correctly considered valid.
	 *
	 * @dataProvider dataIsValid
	 *
	 * @param string $httpMethod The HTTP method.
	 * @param array $parameters Parameters for the query/request.
	 * @param integer $expectedStepNumber The expected step number being requested.
	 */
	public function testIsValid($httpMethod, $parameters, $expectedValid) {
		$flow = $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', array('getName', 'getRequest'));

		$flow->setRevalidatePreviousSteps(false);

		$flow
			->expects($this->any())
			->method('getName')
			->will($this->returnValue('createTopic'))
		;

		$flow
			->expects($this->any())
			->method('getRequest')
			->will($this->returnValue(Request::create('', $httpMethod, $parameters)))
		;

		$dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
		$factory = Forms::createFormFactoryBuilder()->getFormFactory();
		$formBuilder = new FormBuilder(null, null, $dispatcher, $factory);
		$form = $formBuilder->getForm();

		$this->assertSame($expectedValid, $flow->isValid($form));
	}

	public function dataIsValid() {
		return array(
			array('GET', array(), false),
			array('POST', array(), true),
			array('POST', array('flow_createTopic_transition' => 'back'), false),
			array('POST', array('flow_createTopic_transition' => 'reset'), false),
			array('PUT', array(), true),
			array('PUT', array('flow_createTopic_transition' => 'back'), false),
			array('PUT', array('flow_createTopic_transition' => 'reset'), false),
		);
	}

}
