<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Form\StepLabel;
use Craue\FormFlowBundle\Tests\UnitTestCase;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class StepLabelTest extends UnitTestCase {

	/**
	 * @dataProvider dataCreateStringLabel
	 */
	public function testCreateStringLabel($value) {
		$this->assertSame($value, StepLabel::createStringLabel($value)->getText());
	}

	public function dataCreateStringLabel() {
		return array(
			array('label'),
			array('date'),
			array(null),
		);
	}

	/**
	 * @dataProvider dataCreateStringLabel_invalidArgument
	 * @expectedException \Craue\FormFlowBundle\Exception\InvalidTypeException
	 */
	public function testCreateStringLabel_invalidArgument($value) {
		StepLabel::createStringLabel($value);
	}

	public function dataCreateStringLabel_invalidArgument() {
		return array(
			array(true),
			array(1.1),
			array(function() { return 'country'; }),
		);
	}

	/**
	 * @dataProvider dataCreateCallableLabel
	 */
	public function testCreateCallableLabel($value, $expectedText) {
		$this->assertSame($expectedText, StepLabel::createCallableLabel($value)->getText());
	}

	public function dataCreateCallableLabel() {
		return array(
			array('Craue\FormFlowBundle\Tests\Form\StepLabelTest::_returnString', 'label'),
			array('Craue\FormFlowBundle\Tests\Form\StepLabelTest::_returnNull', null),
			array(function() { return 'label'; }, 'label'),
		);
	}

	/**
	 * @dataProvider dataCreateCallableLabel_invalidArgument
	 * @expectedException \InvalidArgumentException
	 */
	public function testCreateCallableLabel_invalidArgument($value) {
		StepLabel::createCallableLabel($value)->getText();
	}

	public function dataCreateCallableLabel_invalidArgument() {
		return array(
			array('label'),
			array('UnknownClass::unknownMethod'),
			array(null),
		);
	}

	/**
	 * @dataProvider dataGetText_callableInvalidReturnValue
	 * @expectedException Craue\FormFlowBundle\Exception\StepLabelCallableInvalidReturnValueException
	 */
	public function testGetText_callableInvalidReturnValue($value) {
		StepLabel::createCallableLabel($value)->getText();
	}

	public function dataGetText_callableInvalidReturnValue() {
		return array(
			array('Craue\FormFlowBundle\Tests\Form\StepLabelTest::_returnOne'),
			array(function() { return 1; }),
		);
	}

	public static function _returnString() {
		return 'label';
	}

	public static function _returnNull() {
		return;
	}

	public static function _returnOne() {
		return 1;
	}

}
