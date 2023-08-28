<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Craue\FormFlowBundle\Form\FormFlowInterface;
use Craue\FormFlowBundle\Form\Step;
use Craue\FormFlowBundle\Form\StepLabel;
use Craue\FormFlowBundle\Tests\UnitTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Test\FormInterface;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2023 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class StepTest extends UnitTestCase {

	public function testCreateFromConfig() {
		$flow = $this->getMockedFlowInterface();

		$step = Step::createFromConfig(1, []);
		$this->assertSame(1, $step->getNumber());
		$this->assertNull($step->getLabel());
		$this->assertNull($step->getFormType());
		$this->assertFalse($step->isSkipped());
		$this->assertEquals([], $step->getFormOptions());
		$step->evaluateSkipping(1, $flow);
		$this->assertFalse($step->isSkipped());

		$step = Step::createFromConfig(1, [
			'label' => 'country',
		]);
		$this->assertEquals('country', $step->getLabel());

		$step = Step::createFromConfig(1, [
			'label' => StepLabel::createCallableLabel(function() {
				return 'country';
			}),
		]);
		$this->assertEquals('country', $step->getLabel());

		$step = Step::createFromConfig(1, [
			'skip' => true,
		]);
		$this->assertTrue($step->isSkipped());
		$step->evaluateSkipping(1, $flow);
		$this->assertTrue($step->isSkipped());

		$step = Step::createFromConfig(1, [
			'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
				return true;
			},
		]);
		$this->assertFalse($step->isSkipped());
		$step->evaluateSkipping(1, $flow);
		$this->assertTrue($step->isSkipped());

		$flowWithData = $this->getMockedFlowInterface();
		$flowWithData
			->expects($this->once())
			->method('getFormData')
			->will($this->returnValue(['blah' => true]))
		;
		$step = Step::createFromConfig(1, [
			'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
				$formData = $flow->getFormData();
				return $estimatedCurrentStepNumber > 1 && $formData['blah'] === true;
			},
		]);
		$step->evaluateSkipping(2, $flowWithData);
		$this->assertTrue($step->isSkipped());

		$form_options = ['foo' => 'bar'];
		$step = Step::createFromConfig(1,[
			'form_options' => $form_options,
		]);
		$this->assertEquals($form_options, $step->getFormOptions());
	}

	public function testCreateFromConfig_invalidOptions() {
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid step config option "lable" given.');

		Step::createFromConfig(1, [
			'lable' => 'label for step1',
		]);
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
		return [
			[1],
		];
	}

	/**
	 * @dataProvider dataSetGetNumber_invalidArguments
	 */
	public function testSetGetNumber_invalidArguments($number) {
		$this->expectException(InvalidTypeException::class);

		$step = new Step();
		$step->setNumber($number);
	}

	public function dataSetGetNumber_invalidArguments() {
		return [
			[null],
			['1'],
			[1.1],
		];
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
		return [
			['label'],
			['date'],
			[null],
		];
	}

	public function testSetGetLabel_callableReturnValueDependsOnFlowData() {
		$flow = $this->getFlowWithMockedMethods(['getFormData']);

		$flow
			->expects($this->exactly(2))
			->method('getFormData')
			->willReturnOnConsecutiveCalls('special', 'default')
		;

		$step = Step::createFromConfig(1, [
			'label' => StepLabel::createCallableLabel(function() use ($flow) {
				return $flow->getFormData() === 'special' ? 'special label' : 'default label';
			}),
		]);

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
		return [
			['label'],
			[null],
		];
	}

	/**
	 * @dataProvider dataSetGetLabel_invalidReturnValueFromCallable
	 */
	public function testSetGetLabel_invalidReturnValueFromCallable($returnValue) {
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('The label callable for step 1 did not return a string or null value.');

		$step = $this->createStepWithLabelCallable(1, $returnValue);
		$step->getLabel();
	}

	public function dataSetGetLabel_invalidReturnValueFromCallable() {
		return [
			[true],
			[false],
			[0],
		];
	}

	/**
	 * @dataProvider dataSetGetLabel_invalidArguments
	 */
	public function testSetGetLabel_invalidArguments($label) {
		$this->expectException(InvalidTypeException::class);

		$step = new Step();
		$step->setLabel($label);
	}

	public function dataSetGetLabel_invalidArguments() {
		return [
			[true],
			[1.1],
			[function() { return 'label'; }],
		];
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
		return [
			[null],
			['myFormType'],
			[$this->createMock(FormTypeInterface::class)],
		];
	}

	/**
	 * @dataProvider dataSetGetFormType_invalidArguments
	 */
	public function testSetGetFormType_invalidArguments($formType) {
		$this->expectException(InvalidTypeException::class);

		$step = new Step();
		$step->setFormType($formType);
	}

	public function dataSetGetFormType_invalidArguments() {
		return [
			[123],
			[$this->createMock(FormInterface::class)],
		];
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
		return [
			[[]],
			[[
				'validation_groups' => ['Default'],
			]],
		];
	}

	/**
	 * @dataProvider dataSetGetFormOptions_invalidArguments
	 */
	public function testSetGetFormOptions_invalidArguments($formOptions) {
		$this->expectException(InvalidTypeException::class);

		$step = new Step();
		$step->setFormOptions($formOptions);
	}

	public function dataSetGetFormOptions_invalidArguments() {
		return [
			[null],
			[true],
			[false],
			[123],
			[new \stdClass()],
		];
	}

	/**
	 * @dataProvider dataSetSkip_invalidArguments
	 */
	public function testSetSkip_invalidArguments($skip) {
		$this->expectException(InvalidTypeException::class);

		$step = new Step();
		$step->setSkip($skip);
	}

	public function dataSetSkip_invalidArguments() {
		return [
			[null],
			[1],
		];
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
		return [
			[true],
			[false],
		];
	}

	/**
	 * @dataProvider dataEvaluateSkipping_invalidReturnValueFromCallable
	 */
	public function testEvaluateSkipping_invalidReturnValueFromCallable($returnValue) {
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('The skip callable for step 1 did not return a boolean value.');

		$step = $this->createStepWithSkipCallable(1, $returnValue);
		$step->evaluateSkipping(1, $this->getMockedFlowInterface());
	}

	public function dataEvaluateSkipping_invalidReturnValueFromCallable() {
		return [
			[null],
			[0],
			['true'],
		];
	}

	protected function createStepWithLabelCallable($number, $returnValue) {
		return Step::createFromConfig($number, [
			'label' => StepLabel::createCallableLabel(function() use ($returnValue) {
				return $returnValue;
			}),
		]);
	}

	protected function createStepWithSkipCallable($number, $returnValue) {
		return Step::createFromConfig($number, [
			'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) use ($returnValue) {
				return $returnValue;
			},
		]);
	}

}
