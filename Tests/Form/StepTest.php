<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Form\FormFlowInterface;
use Craue\FormFlowBundle\Form\Step;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class StepTest extends \PHPUnit_Framework_TestCase {

	public function testCreateFromConfig() {
		$flowStub = $this->getMock('\Craue\FormFlowBundle\Form\FormFlowInterface');

		$step = Step::createFromConfig(1, array());
		$this->assertSame(1, $step->getNumber());
		$this->assertNull($step->getLabel());
		$this->assertNull($step->getType());
		$this->assertFalse($step->isSkipped());
		$step->evaluateSkipping(1, $flowStub);
		$this->assertFalse($step->isSkipped());

		$step = Step::createFromConfig(1, array(
			'label' => 'country',
		));
		$this->assertEquals('country', $step->getLabel());

		$step = Step::createFromConfig(1, array(
			'skip' => true,
		));
		$this->assertTrue($step->isSkipped());
		$step->evaluateSkipping(1, $flowStub);
		$this->assertTrue($step->isSkipped());

		$step = Step::createFromConfig(1, array(
			'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
				return true;
			},
		));
		$this->assertFalse($step->isSkipped());
		$step->evaluateSkipping(1, $flowStub);
		$this->assertTrue($step->isSkipped());

		$flowStubWithData = $this->getMock('\Craue\FormFlowBundle\Form\FormFlowInterface');
		$flowStubWithData
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
		$step->evaluateSkipping(2, $flowStubWithData);
		$this->assertTrue($step->isSkipped());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Invalid step config option "lable" given.
	 */
	public function testCreateFromConfig_invalidOptions() {
		$step = Step::createFromConfig(1, array(
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
			array(null),
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
		);
	}

	/**
	 * @dataProvider dataSetGetType
	 */
	public function testSetGetType($type) {
		$step = new Step();
		$step->setType($type);
		$this->assertEquals($type, $step->getType());
	}

	public function dataSetGetType() {
		return array(
			array(null),
			array('myFormType'),
			array($this->getMock('Symfony\Component\Form\FormTypeInterface')),
		);
	}

	/**
	 * @dataProvider dataSetGetType_invalidArguments
	 * @expectedException \Craue\FormFlowBundle\Exception\InvalidTypeException
	 */
	public function testSetGetType_invalidArguments($type) {
		$step = new Step();
		$step->setType($type);
	}

	public function dataSetGetType_invalidArguments() {
		return array(
			array(123),
			array($this->getMock('Symfony\Component\Form\Test\FormInterface')),
		);
	}

	/**
	 * @dataProvider dataEvaluateSkipping_validReturnValueFromCallable
	 */
	public function testEvaluateSkipping_validReturnValueFromCallable($returnValue) {
		$step = $this->createStepWithSkipCallable(1, $returnValue);
		$step->evaluateSkipping(1, $this->getMock('\Craue\FormFlowBundle\Form\FormFlowInterface'));
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
		$step->evaluateSkipping(1, $this->getMock('\Craue\FormFlowBundle\Form\FormFlowInterface'));
	}

	public function dataEvaluateSkipping_invalidReturnValueFromCallable() {
		return array(
			array(null),
			array(0),
			array('true'),
		);
	}

	protected function createStepWithSkipCallable($number, $returnValue) {
		return Step::createFromConfig($number, array(
			'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) use ($returnValue) {
				return $returnValue;
			},
		));
	}

}
