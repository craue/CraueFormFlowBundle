<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Event\GetStepsEvent;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Craue\FormFlowBundle\Tests\UnitTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @group unit
 *
 * @author Toni Uebernickel <tuebernickel@gmail.com>
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowTest extends UnitTestCase {

	public function testStepListener() {
		$steps = array(
			$this->getMockedStepInterface(),
		);

		$dispatcher = new EventDispatcher();
		$dispatcher->addListener(FormFlowEvents::GET_STEPS, function(GetStepsEvent $event) use ($steps) {
			$event->setSteps($steps);

			$event->stopPropagation();
		});

		$flow = $this->getMockedFlow();
		$flow->setEventDispatcher($dispatcher);

		$this->assertEquals($steps, $flow->getSteps());
	}

	public function testCreateStepsFromConfig_fixArrayIndexes() {
		$flow = $this->getFlowWithMockedMethods(array('loadStepsConfig'));

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
	 * Ensure that the generated step-based validation group is added.
	 */
	public function testGetFormOptions_addGeneratedValidationGroup() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'loadStepsConfig'));

		$flow
			->expects($this->once())
			->method('getName')
			->will($this->returnValue('createTopic'))
		;

		$flow
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue(array(
				array(
					'form_options' => array(
						'validation_groups' => 'Default',
					)
				),
			)))
		;

		$options = $flow->getFormOptions(1);

		$this->assertEquals(array('flow_createTopic_step1', 'Default'), $options['validation_groups']);
	}

	/**
	 * Ensure that the "validation_groups" option can be set to false to disable validation.
	 */
	public function testGetFormOptions_setValidationGroupsToFalse() {
		$flow = $this->getFlowWithMockedMethods(array('loadStepsConfig'));

		$flow
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue(array(
				array(),
			)))
		;

		$options = $flow->getFormOptions(1, array(
			'validation_groups' => false,
		));

		$this->assertFalse($options['validation_groups']);
	}

	/**
	 * Ensure that the "validation_groups" option can be set to a closure.
	 */
	public function testGetFormOptions_setValidationGroupsToClosure() {
		$flow = $this->getFlowWithMockedMethods(array('loadStepsConfig'));

		$flow
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue(array(
				array(),
			)))
		;

		$options = $flow->getFormOptions(1, array(
			'validation_groups' => function(FormInterface $form) {
				return array('custom_group');
			},
		));

		$this->assertTrue(is_callable($options['validation_groups']));
	}

	/**
	 * Ensure that the generated step-based value for "validation_groups" is an array, which can be used to just add
	 * other groups.
	 */
	public function testGetFormOptions_generatedValidationGroupIsArray() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'loadStepsConfig'));

		$flow
			->expects($this->once())
			->method('getName')
			->will($this->returnValue('createTopic'))
		;

		$flow
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue(array(
				array(),
			)))
		;

		$options = $flow->getFormOptions(1);

		$this->assertEquals(array('flow_createTopic_step1'), $options['validation_groups']);
	}

	public function testGetStepsDoneRemaining() {
		$flow = $this->getFlowWithMockedMethods(array('loadStepsConfig', 'retrieveStepData'));

		$flow
			->method('retrieveStepData')
			->will($this->returnValue(array()))
		;

		$flow
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue(array(
				array(
					'label' => 'step1',
					'skip' => true,
				),
				array(
					'label' => 'step2'
				),
				array(
					'label' => 'step3'
				),
			)))
		;

		$stepsDone = $flow->getStepsDone();
		$stepsRemaining = $flow->getStepsRemaining();

		$this->assertSame(1, current($stepsDone)->getNumber());
		$this->assertSame(1, end($stepsDone)->getNumber());

		$this->assertSame(2, current($stepsRemaining)->getNumber());
		$this->assertSame(3, end($stepsRemaining)->getNumber());

		$this->assertSame(1, $flow->getStepsDoneCount());
		$this->assertSame(2, $flow->getStepsRemainingCount());
	}

	/**
	 * Ensure that generic options are considered.
	 */
	public function testGetFormOptions_considerGenericOptions() {
		$flow = $this->getFlowWithMockedMethods(array('loadStepsConfig'));

		$flow
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue(array(
				array(),
			)))
		;

		$flow->setGenericFormOptions(array(
			'action' => 'targetUrl',
		));

		$options = $flow->getFormOptions(1);

		$this->assertEquals('targetUrl', $options['action']);
	}

	/**
	 * Ensure that step specific options override generic options.
	 */
	public function testGetFormOptions_considerStepSpecificOptions() {
		$flow = $this->getFlowWithMockedMethods(array('loadStepsConfig'));

		$flow
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue(array(
				array(
					'form_options' => array(
						'action' => 'specificTargetUrl',
					)
				),
			)))
		;

		$flow->setGenericFormOptions(array(
			'action' => 'targetUrl',
		));

		$options = $flow->getFormOptions(1);

		$this->assertEquals('specificTargetUrl', $options['action']);
	}

	/**
	 * Ensure that options can be overridden directly.
	 */
	public function testGetFormOptions_considerDirectlyPassedOptions() {
		$flow = $this->getFlowWithMockedMethods(array('loadStepsConfig'));

		$flow
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue(array(
				array(
					'form_options' => array(
						'action' => 'specificTargetUrl',
					)
				),
			)))
		;

		$flow->setGenericFormOptions(array(
			'action' => 'targetUrl',
		));

		$options = $flow->getFormOptions(1, array(
			'action' => 'finalTargetUrl',
		));

		$this->assertEquals('finalTargetUrl', $options['action']);
	}

	/**
	 * Ensure that the requested step number is correctly determined.
	 *
	 * @dataProvider dataGetRequestedStepNumber
	 *
	 * @param string $httpMethod The HTTP method.
	 * @param array $parameters Parameters for the query/request.
	 * @param bool $dsnEnabled If dynamic step navigation is enabled.
	 * @param int $expectedStepNumber The expected step number being requested.
	 */
	public function testGetRequestedStepNumber($httpMethod, $parameters, $dsnEnabled, $expectedStepNumber) {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'getRequest'));

		if ($dsnEnabled) {
			$flow->setAllowDynamicStepNavigation(true);
		}

		$flow
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
	 * @param bool $expectedValid If the form is expected to be valid.
	 */
	public function testIsValid($httpMethod, $parameters, $expectedValid) {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'getRequest'));

		$flow->setRevalidatePreviousSteps(false);

		$flow
			->method('getName')
			->will($this->returnValue('createTopic'))
		;

		$flow
			->method('getRequest')
			->will($this->returnValue(Request::create('', $httpMethod, $parameters)))
		;

		// TODO replace by `$this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')` as soon as PHPUnit >= 5.4 is required
		$dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
		$factory = Forms::createFormFactoryBuilder()->getFormFactory();
		$formBuilder = new FormBuilder(null, 'stdClass', $dispatcher, $factory);
		$useFqcn = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');

		$form = $formBuilder
			->setCompound(true)
			// TODO replace by `$this->createMock('Symfony\Component\Form\DataMapperInterface')` as soon as PHPUnit >= 5.4 is required
			->setDataMapper($this->getMockBuilder('Symfony\Component\Form\DataMapperInterface')->getMock())
			->add('aField', $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\TextType' : 'text')
			->setMethod($httpMethod)
			->setRequestHandler(new HttpFoundationRequestHandler())
			->getForm()
		;

		$this->assertSame($expectedValid, $flow->isValid($form));
	}

	public function dataIsValid() {
		$defaultData = array('aField' => '');

		return array(
			array('GET', array(), false),
			array('GET', $defaultData, false),
			array('POST', array(), false),
			array('POST', $defaultData, true),
			array('POST', array_merge($defaultData, array('flow_createTopic_transition' => 'back')), false),
			array('POST', array_merge($defaultData, array('flow_createTopic_transition' => 'reset')), false),
			array('PUT', array(), false),
			array('PUT', $defaultData, true),
			array('PUT', array_merge($defaultData, array('flow_createTopic_transition' => 'back')), false),
			array('PUT', array_merge($defaultData, array('flow_createTopic_transition' => 'reset')), false),
		);
	}

	public function testSetGetRequestStack() {
		$flow = $this->getMockedFlow();

		$request = Request::create('');
		$requestStack = new RequestStack();
		$requestStack->push($request);
		$flow->setRequestStack($requestStack);

		$this->assertSame($request, $flow->getRequest());
	}

	/**
	 * @expectedException \RuntimeException
	 * @expectedExceptionMessage The request is not available.
	 */
	public function testGetRequestStack_notAvailable() {
		$flow = $this->getMockedFlow();
		$flow->setRequestStack(new RequestStack());
		$flow->getRequest();
	}

	public function testSetGetDataManager() {
		$flow = $this->getMockedFlow();

		$dataManager = $this->getMockedDataManagerInterface();
		$flow->setDataManager($dataManager);

		$this->assertSame($dataManager, $flow->getDataManager());
	}

	public function testSetGetId() {
		$flow = $this->getMockedFlow();

		$id = 'flow-id';
		$flow->setId($id);

		$this->assertEquals($id, $flow->getId());
	}

	public function testSetGetInstanceKey() {
		$flow = $this->getMockedFlow();

		$instanceKey = 'instance-key';
		$flow->setInstanceKey($instanceKey);

		$this->assertEquals($instanceKey, $flow->getInstanceKey());
	}

	public function testSetGetInstanceId() {
		$flow = $this->getMockedFlow();

		$instanceId = 'instance-id';
		$flow->setInstanceId($instanceId);

		$this->assertEquals($instanceId, $flow->getInstanceId());
	}

	public function testSetGetFormStepKey() {
		$flow = $this->getMockedFlow();

		$formStepKey = 'form-step-key';
		$flow->setFormStepKey($formStepKey);

		$this->assertEquals($formStepKey, $flow->getFormStepKey());
	}

	public function testSetGetFormTransitionKey() {
		$flow = $this->getMockedFlow();

		$formTransitionKey = 'form-transition-key';
		$flow->setFormTransitionKey($formTransitionKey);

		$this->assertEquals($formTransitionKey, $flow->getFormTransitionKey());
	}

	public function testSetGetValidationGroupPrefix() {
		$flow = $this->getMockedFlow();

		$validationGroupPrefix = 'validation-group-prefix';
		$flow->setValidationGroupPrefix($validationGroupPrefix);

		$this->assertEquals($validationGroupPrefix, $flow->getValidationGroupPrefix());
	}

	/**
	 * @dataProvider dataBooleanSetter
	 */
	public function testSetIsRevalidatePreviousSteps($expectedValue, $revalidatePreviousSteps) {
		$flow = $this->getMockedFlow();

		$flow->setRevalidatePreviousSteps($revalidatePreviousSteps);

		$this->assertEquals($expectedValue, $flow->isRevalidatePreviousSteps());
	}

	/**
	 * @dataProvider dataBooleanSetter
	 */
	public function testSetIsAllowDynamicStepNavigation($expectedValue, $allowDynamicStepNavigation) {
		$flow = $this->getMockedFlow();

		$flow->setAllowDynamicStepNavigation($allowDynamicStepNavigation);

		$this->assertEquals($expectedValue, $flow->isAllowDynamicStepNavigation());
	}

	/**
	 * @dataProvider dataBooleanSetter
	 */
	public function testSetIsHandleFileUploads($expectedValue, $handleFileUploads) {
		$flow = $this->getMockedFlow();

		$flow->setHandleFileUploads($handleFileUploads);

		$this->assertEquals($expectedValue, $flow->isHandleFileUploads());
	}

	/**
	 * @dataProvider dataSetGetHandleFileUploadsTempDir
	 */
	public function testSetGetHandleFileUploadsTempDir($expectedValue, $handleFileUploadsTempDir) {
		$flow = $this->getMockedFlow();

		$flow->setHandleFileUploadsTempDir($handleFileUploadsTempDir);

		$this->assertEquals($expectedValue, $flow->getHandleFileUploadsTempDir());
	}

	public function dataSetGetHandleFileUploadsTempDir() {
		return array(
			array(null, null),
			array('1', 1),
			array('/tmp', '/tmp'),
		);
	}

	/**
	 * @dataProvider dataBooleanSetter
	 */
	public function testSetIsAllowRedirectAfterSubmit($expectedValue, $allowRedirectAfterSubmit) {
		$flow = $this->getMockedFlow();

		$flow->setAllowRedirectAfterSubmit($allowRedirectAfterSubmit);

		$this->assertEquals($expectedValue, $flow->isAllowRedirectAfterSubmit());
	}

	public function testSetGetDynamicStepNavigationInstanceParameter() {
		$flow = $this->getMockedFlow();

		$dynamicStepNavigationInstanceParameter = 'dsn-instance';
		$flow->setDynamicStepNavigationInstanceParameter($dynamicStepNavigationInstanceParameter);

		$this->assertEquals($dynamicStepNavigationInstanceParameter, $flow->getDynamicStepNavigationInstanceParameter());
	}

	public function testSetGetDynamicStepNavigationStepParameter() {
		$flow = $this->getMockedFlow();

		$dynamicStepNavigationStepParameter = 'dsn-step';
		$flow->setDynamicStepNavigationStepParameter($dynamicStepNavigationStepParameter);

		$this->assertEquals($dynamicStepNavigationStepParameter, $flow->getDynamicStepNavigationStepParameter());
	}

	/**
	 * @expectedException \RuntimeException
	 * @expectedExceptionMessage Form data has not been evaluated yet and thus cannot be accessed.
	 */
	public function testGetFormData_notAvailable() {
		$this->getMockedFlow()->getFormData();
	}

	/**
	 * @expectedException \RuntimeException
	 * @expectedExceptionMessage The current step has not been determined yet and thus cannot be accessed.
	 */
	public function testGetCurrentStepNumber_notAvailable() {
		$this->getMockedFlow()->getCurrentStepNumber();
	}

	/**
	 * @dataProvider dataApplySkipping_invalidArguments
	 * @expectedException \InvalidArgumentException
	 */
	public function testApplySkipping_invalidArguments($direction) {
		$flow = $this->getMockedFlow();

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
		$this->getMockedFlow()->getStep($stepNumber);
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
		$this->getMockedFlow()->getStep($stepNumber);
	}

	public function dataGetStep_invalidStep() {
		return array(
			array(2),
		);
	}

	public function testGetCurrentStepLabel() {
		$label = 'step1';

		$flow = $this->getFlowWithMockedMethods(array('loadStepsConfig'));

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
		$flow = $this->getMockedFlow();

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

}
