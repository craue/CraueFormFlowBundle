<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Form\FormFlowInterface;
use Craue\FormFlowBundle\Form\Step;
use Craue\FormFlowBundle\Form\StepLabel;
use Craue\FormFlowBundle\Tests\UnitTestCase;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class StepTest extends UnitTestCase {

	public function testCreateFromConfig() {
		$flow = $this->getMockedFlowInterface();

		$step = Step::createFromConfig(1, array());
		$this->assertSame(1, $step->getNumber());
		$this->assertNull($step->getLabel());
		$this->assertNull($step->getFormType());
		$this->assertFalse($step->isSkipped());
		$this->assertEquals(array(), $step->getFormOptions());
		$step->evaluateSkipping(1, $flow);
		$this->assertFalse($step->isSkipped());

		$step = Step::createFromConfig(1, array(
			'label' => 'country',
		));
		$this->assertEquals('country', $step->getLabel());

		$step = Step::createFromConfig(1, array(
			'label' => StepLabel::createCallableLabel(function() {
				return 'country';
			}),
		));
		$this->assertEquals('country', $step->getLabel());

		$step = Step::createFromConfig(1, array(
			'skip' => true,
		));
		$this->assertTrue($step->isSkipped());
		$step->evaluateSkipping(1, $flow);
		$this->assertTrue($step->isSkipped());

		$step = Step::createFromConfig(1, array(
			'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
				return true;
			},
		));
		$this->assertFalse($step->isSkipped());
		$step->evaluateSkipping(1, $flow);
		$this->assertTrue($step->isSkipped());

		$flowWithData = $this->getMockedFlowInterface();
		$flowWithData
			->expects($this->once())
			->method('getFormData')
			->will($this->returnValue(array('blah' => true)))
		;
		$step = Step::createFromConfig(1, array(
			'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
				$formData = $flow->getFormData();
				return $estimatedCurrentStepNumber > 1 && $formData['blah'] === true;
			},
		));
		$step->evaluateSkipping(2, $flowWithData);
		$this->assertTrue($step->isSkipped());

		$form_options = array('foo' => 'bar');
		$step = Step::createFromConfig(1, array(
			'form_options' => $form_options,
		));
		$this->assertEquals($form_options, $step->getFormOptions());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Invalid step config option "lable" given.
	 */
	public function testCreateFromConfig_invalidOptions() {
		Step::createFromConfig(1, array(
			'lable' => 'label for step1',
		));
	}

	/**
	 * @dataProvider dataSetGetNumber
	 */
	public function testSetGetNumber($number) {
		$step = new Step();
		$step->setNumber($number);
		$this->assertSame($number, $step->getNumber());
	}

	public function dataSetGetNumber() {
		return array(
			array(1),
		);
	}

	/**
	 * @dataProvider dataSetGetNumber_invalidArguments
	 * @expectedException \Craue\FormFlowBundle\Exception\InvalidTypeException
	 */
	public function testSetGetNumber_invalidArguments($number) {
		$step = new Step();
		$step->setNumber($number);
	}

	public function dataSetGetNumber_invalidArguments() {
		return array(
			array(null),
			array('1'),
			array(1.1),
		);
	}

	/**
	 * @dataProvider dataSetGetLabel
	 */
	public function testSetGetLabel($label) {
		$step = new Step();
		$step->setLabel($label);
		$this->assertSame($label, $step->getLabel());
	}

	public function dataSetGetLabel() {
		return array(
			array('label'),
			array('date'),
			array(null),
		);
	}

	public function testSetGetLabel_callableReturnValueDependsOnFlowData() {
		$flow = $this->getFlowWithMockedMethods(array('getFormData'));

		$flow
			->expects($this->at(0))
			->method('getFormData')
			->will($this->returnValue('special'))
		;

		$flow
			->expects($this->at(1))
			->method('getFormData')
			->will($this->returnValue('default'))
		;

		$step = Step::createFromConfig(1, array(
			'label' => StepLabel::createCallableLabel(function() use ($flow) {
				return $flow->getFormData() === 'special' ? 'special label' : 'default label';
			}),
		));

		$this->assertSame('special label', $step->getLabel());
		$this->assertSame('default label', $step->getLabel());
	}

	/**
	 * @dataProvider dataSetGetLabel_validReturnValueFromCallable
	 */
	public function testSetGetLabel_validReturnValueFromCallable($returnValue) {
		$step = $this->createStepWithLabelCallable(1, $returnValue);
		$this->assertSame($returnValue, $step->getLabel());
	}

	public function dataSetGetLabel_validReturnValueFromCallable() {
		return array(
			array('label'),
			array(null),
		);
	}

	/**
	 * @dataProvider dataSetGetLabel_invalidReturnValueFromCallable
	 * @expectedException \RuntimeException
	 * @expectedExceptionMessage The label callable for step 1 did not return a string or null value.
	 */
	public function testSetGetLabel_invalidReturnValueFromCallable($returnValue) {
		$step = $this->createStepWithLabelCallable(1, $returnValue);
		$step->getLabel();
	}

	public function dataSetGetLabel_invalidReturnValueFromCallable() {
		return array(
			array(true),
			array(false),
			array(0),
		);
	}

	/**
	 * @dataProvider dataSetGetLabel_invalidArguments
	 * @expectedException \Craue\FormFlowBundle\Exception\InvalidTypeException
	 */
	public function testSetGetLabel_invalidArguments($label) {
		$step = new Step();
		$step->setLabel($label);
	}

	public function dataSetGetLabel_invalidArguments() {
		return array(
			array(true),
			array(1.1),
			array(function() { return 'label'; }),
		);
	}

	/**
	 * @dataProvider dataSetGetFormType
	 */
	public function testSetGetFormType($formType) {
		$step = new Step();
		$step->setFormType($formType);
		$this->assertSame($formType, $step->getFormType());
	}

	public function dataSetGetFormType() {
		return array(
			array(null),
			array('myFormType'),
			// TODO replace by `$this->createMock('Symfony\Component\Form\FormTypeInterface')` as soon as PHPUnit >= 5.4 is required
			array($this->getMockBuilder('Symfony\Component\Form\FormTypeInterface')->getMock()),
		);
	}

	/**
	 * @dataProvider dataSetGetFormType_invalidArguments
	 * @expectedException \Craue\FormFlowBundle\Exception\InvalidTypeException
	 */
	public function testSetGetFormType_invalidArguments($formType) {
		$step = new Step();
		$step->setFormType($formType);
	}

	public function dataSetGetFormType_invalidArguments() {
		return array(
			array(123),
			// TODO replace by `$this->createMock('Symfony\Component\Form\Test\FormInterface')` as soon as PHPUnit >= 5.4 is required
			array($this->getMockBuilder('Symfony\Component\Form\Test\FormInterface')->getMock()),
		);
	}

	/**
	 * @dataProvider dataSetGetFormOptions
	 */
	public function testSetGetFormOptions($formOptions) {
		$step = new Step();
		$step->setFormOptions($formOptions);
		$this->assertEquals($formOptions, $step->getFormOptions());
	}

	public function dataSetGetFormOptions() {
		return array(
			array(array()),
			array(array(
				'validation_groups' => array('Default'),
			)),
		);
	}

	/**
	 * @dataProvider dataSetGetFormOptions_invalidArguments
	 * @expectedException \Craue\FormFlowBundle\Exception\InvalidTypeException
	 */
	public function testSetGetFormOptions_invalidArguments($formOptions) {
		$step = new Step();
		$step->setFormOptions($formOptions);
	}

	public function dataSetGetFormOptions_invalidArguments() {
		return array(
			array(null),
			array(true),
			array(false),
			array(123),
			array(new \stdClass()),
		);
	}

	/**
	 * @dataProvider dataSetSkip_invalidArguments
	 * @expectedException \Craue\FormFlowBundle\Exception\InvalidTypeException
	 */
	public function testSetSkip_invalidArguments($skip) {
		$step = new Step();
		$step->setSkip($skip);
	}

	public function dataSetSkip_invalidArguments() {
		return array(
			array(null),
			array(1),
		);
	}

	/**
	 * @dataProvider dataEvaluateSkipping_validReturnValueFromCallable
	 */
	public function testEvaluateSkipping_validReturnValueFromCallable($returnValue) {
		$step = $this->createStepWithSkipCallable(1, $returnValue);
		$step->evaluateSkipping(1, $this->getMockedFlowInterface());
		$this->assertSame($returnValue, $step->isSkipped());
	}

	public function dataEvaluateSkipping_validReturnValueFromCallable() {
		return array(
			array(true),
			array(false),
		);
	}

	/**
	 * @dataProvider dataEvaluateSkipping_invalidReturnValueFromCallable
	 * @expectedException \RuntimeException
	 * @expectedExceptionMessage The skip callable for step 1 did not return a boolean value.
	 */
	public function testEvaluateSkipping_invalidReturnValueFromCallable($returnValue) {
		$step = $this->createStepWithSkipCallable(1, $returnValue);
		$step->evaluateSkipping(1, $this->getMockedFlowInterface());
	}

	public function dataEvaluateSkipping_invalidReturnValueFromCallable() {
		return array(
			array(null),
			array(0),
			array('true'),
		);
	}

	protected function createStepWithLabelCallable($number, $returnValue) {
		return Step::createFromConfig($number, array(
			'label' => StepLabel::createCallableLabel(function() use ($returnValue) {
				return $returnValue;
			}),
		));
	}

	protected function createStepWithSkipCallable($number, $returnValue) {
		return Step::createFromConfig($number, array(
			'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) use ($returnValue) {
				return $returnValue;
			},
		));
	}

}
