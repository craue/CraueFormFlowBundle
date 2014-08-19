<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Event\GetStepsEvent;
use Craue\FormFlowBundle\Form\FormFlow;
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

		$flow = $this->getFlowMock();
		$flow->setEventDispatcher($dispatcher);

		$this->assertEquals($steps, $flow->getSteps());
	}

	public function testCreateStepsFromConfig_fixArrayIndexes() {
		$flow = $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', array('getName', 'loadStepsConfig'));

		$flow
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue(array(
				2 => array(
					'label' => 'step1',
				),
			)))
		;

		$this->assertSame(1, $flow->getStep(1)->getNumber());
	}

	/**
	 * Ensure that the "validation_groups" option can be overridden.
	 */
	public function testGetFormOptions_overrideValidationGroups() {
		$options = $this->getFlowMock()->getFormOptions(1, array(
			'validation_groups' => 'Default',
		));

		$this->assertEquals('Default', $options['validation_groups']);
	}

	/**
	 * Ensure that the generated default value for "validation_groups" is an array, which can be used to just add
	 * other groups.
	 */
	public function testGetFormOptions_generatedValidationGroupIsArray() {
		$flow = $this->getFlowMock();

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
		$flow = $this->getFlowMock();

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
			array('BLAH', array(), false, 1), // fallback on invalid method
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

	public function testSetGetRequest() {
		$flow = $this->getFlowMock();

		$request = Request::create('');
		$flow->setRequest($request);

		$this->assertSame($request, $flow->getRequest());
	}

	public function testSetGetStorage() {
		$flow = $this->getFlowMock();

		$storage = $this->getMock('\Craue\FormFlowBundle\Storage\StorageInterface');
		$flow->setStorage($storage);

		$this->assertSame($storage, $flow->getStorage());
	}

	public function testSetGetId() {
		$flow = $this->getFlowMock();

		$id = 'flow-id';
		$flow->setId($id);

		$this->assertEquals($id, $flow->getId());
	}

	public function testSetGetInstanceKey() {
		$flow = $this->getFlowMock();

		$instanceKey = 'instance-key';
		$flow->setInstanceKey($instanceKey);

		$this->assertEquals($instanceKey, $flow->getInstanceKey());
	}

	public function testSetGetInstanceId() {
		$flow = $this->getFlowMock();

		$instanceId = 'instance-id';
		$flow->setInstanceId($instanceId);

		$this->assertEquals($instanceId, $flow->getInstanceId());
	}

	public function testSetGetFormStepKey() {
		$flow = $this->getFlowMock();

		$formStepKey = 'form-step-key';
		$flow->setFormStepKey($formStepKey);

		$this->assertEquals($formStepKey, $flow->getFormStepKey());
	}

	public function testSetGetFormTransitionKey() {
		$flow = $this->getFlowMock();

		$formTransitionKey = 'form-transition-key';
		$flow->setFormTransitionKey($formTransitionKey);

		$this->assertEquals($formTransitionKey, $flow->getFormTransitionKey());
	}

	public function testSetGetStepDataKey() {
		$flow = $this->getFlowMock();

		$stepDataKey = 'step-data-key';
		$flow->setStepDataKey($stepDataKey);

		$this->assertEquals($stepDataKey, $flow->getStepDataKey());
	}

	public function testSetGetValidationGroupPrefix() {
		$flow = $this->getFlowMock();

		$validationGroupPrefix = 'validation-group-prefix';
		$flow->setValidationGroupPrefix($validationGroupPrefix);

		$this->assertEquals($validationGroupPrefix, $flow->getValidationGroupPrefix());
	}

	/**
	 * @dataProvider dataBooleanSetter
	 */
	public function testSetIsRevalidatePreviousSteps($expectedValue, $revalidatePreviousSteps) {
		$flow = $this->getFlowMock();

		$flow->setRevalidatePreviousSteps($revalidatePreviousSteps);

		$this->assertEquals($expectedValue, $flow->isRevalidatePreviousSteps());
	}

	/**
	 * @dataProvider dataBooleanSetter
	 */
	public function testSetIsAllowDynamicStepNavigation($expectedValue, $allowDynamicStepNavigation) {
		$flow = $this->getFlowMock();

		$flow->setAllowDynamicStepNavigation($allowDynamicStepNavigation);

		$this->assertEquals($expectedValue, $flow->isAllowDynamicStepNavigation());
	}

	public function testSetGetDynamicStepNavigationInstanceParameter() {
		$flow = $this->getFlowMock();

		$dynamicStepNavigationInstanceParameter = 'dsn-instance';
		$flow->setDynamicStepNavigationInstanceParameter($dynamicStepNavigationInstanceParameter);

		$this->assertEquals($dynamicStepNavigationInstanceParameter, $flow->getDynamicStepNavigationInstanceParameter());
	}

	public function testSetGetDynamicStepNavigationStepParameter() {
		$flow = $this->getFlowMock();

		$dynamicStepNavigationStepParameter = 'dsn-step';
		$flow->setDynamicStepNavigationStepParameter($dynamicStepNavigationStepParameter);

		$this->assertEquals($dynamicStepNavigationStepParameter, $flow->getDynamicStepNavigationStepParameter());
	}

	/**
	 * @expectedException \RuntimeException
	 * @expectedExceptionMessage The request is not available.
	 */
	public function testGetRequest_notAvailable() {
		$this->getFlowMock()->getRequest();
	}

	/**
	 * @expectedException \RuntimeException
	 * @expectedExceptionMessage Form data has not been evaluated yet and thus cannot be accessed.
	 */
	public function testGetFormData_notAvailable() {
		$this->getFlowMock()->getFormData();
	}

	/**
	 * @expectedException \RuntimeException
	 * @expectedExceptionMessage The current step has not been determined yet and thus cannot be accessed.
	 */
	public function testGetCurrentStepNumber_notAvailable() {
		$this->getFlowMock()->getCurrentStepNumber();
	}

	/**
	 * @dataProvider dataApplySkipping_invalidArguments
	 * @expectedException \InvalidArgumentException
	 */
	public function testApplySkipping_invalidArguments($direction) {
		$flow = $this->getFlowMock();

		$method = new \ReflectionMethod($flow, 'applySkipping');
		$method->setAccessible(true);

		$method->invoke($flow, 1, $direction);
	}

	public function dataApplySkipping_invalidArguments() {
		return array(
			array(2),
			array(-2),
			array(null),
		);
	}

	/**
	 * @dataProvider dataGetStep_invalidArguments
	 * @expectedException \Craue\FormFlowBundle\Exception\InvalidTypeException
	 */
	public function testGetStep_invalidArguments($stepNumber) {
		$this->getFlowMock()->getStep($stepNumber);
	}

	public function dataGetStep_invalidArguments() {
		return array(
			array('a'),
			array(null),
		);
	}

	/**
	 * @dataProvider dataGetStep_invalidStep
	 * @expectedException \OutOfBoundsException
	 * @expectedExceptionMessage The step "2" does not exist.
	 */
	public function testGetStep_invalidStep($stepNumber) {
		$this->getFlowMock()->getStep($stepNumber);
	}

	public function dataGetStep_invalidStep() {
		return array(
			array(2),
		);
	}

	public function testGetCurrentStepLabel() {
		$label = 'step1';

		$flow = $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', array('getName', 'loadStepsConfig'));

		$flow
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue(array(
				array(
					'label' => $label,
				),
			)))
		;

		$flow->nextStep();

		$this->assertEquals($label, $flow->getCurrentStepLabel());
	}

	public function testLoadStepsConfig() {
		$flow = $this->getFlowMock();

		$method = new \ReflectionMethod($flow, 'loadStepsConfig');
		$method->setAccessible(true);

		$this->assertEquals(array(), $method->invoke($flow));
	}

	public function dataBooleanSetter() {
		return array(
			array(true, true),
			array(false, false),
			array(true, 1),
			array(false, 0),
			array(false, null),
		);
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|FormFlow
	 */
	protected function getFlowMock() {
		return $this->getMockForAbstractClass('\Craue\FormFlowBundle\Form\FormFlow');
	}

}
