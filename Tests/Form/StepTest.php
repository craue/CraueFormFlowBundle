<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Form\FormFlowInterface;
use Craue\FormFlowBundle\Form\Step;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2013 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class StepTest extends \PHPUnit_Framework_TestCase {

	public function testCreateFromConfig() {
		$flowStub = $this->getMock('\Craue\FormFlowBundle\Form\FormFlowInterface');

		$step = Step::createFromConfig(1, array());
		$this->assertEquals(1, $step->getNumber());
		$this->assertEquals(null, $step->getLabel());
		$this->assertEquals(null, $step->getType());
		$this->assertEquals(false, $step->isSkipped());
		$step->evaluateSkipping(1, $flowStub);
		$this->assertEquals(false, $step->isSkipped());

		$step = Step::createFromConfig(1, array(
			'label' => 'country',
		));
		$this->assertEquals('country', $step->getLabel());

		$step = Step::createFromConfig(1, array(
			'skip' => true,
		));
		$this->assertEquals(true, $step->isSkipped());
		$step->evaluateSkipping(1, $flowStub);
		$this->assertEquals(true, $step->isSkipped());

		$step = Step::createFromConfig(1, array(
			'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
				return true;
			},
		));
		$this->assertEquals(null, $step->isSkipped());
		$step->evaluateSkipping(1, $flowStub);
		$this->assertEquals(true, $step->isSkipped());

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
		$this->assertEquals(true, $step->isSkipped());
	}

	/**
	 * @dataProvider dataSetGetNumber
	 */
	public function testSetGetNumber($number) {
		$step = new Step();
		$step->setNumber($number);
		$this->assertEquals($number, $step->getNumber());
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
		$this->assertEquals($label, $step->getLabel());
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
	 * @dataProvider dataEvaluateSkipping_invalidReturnValueFromCallable
	 * @expectedException \RuntimeException
	 * @expectedExceptionMessage The callable did not return a boolean value.
	 */
	public function testEvaluateSkipping_invalidReturnValueFromCallable($returnValue) {
		$step = Step::createFromConfig(1, array(
			'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) use ($returnValue) {
				return $returnValue;
			},
		));
		$step->evaluateSkipping(1, $this->getMock('\Craue\FormFlowBundle\Form\FormFlowInterface'));
	}

	public function dataEvaluateSkipping_invalidReturnValueFromCallable() {
		return array(
			array(null),
			array(0),
		);
	}

}
