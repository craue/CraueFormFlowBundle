<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Craue\FormFlowBundle\Exception\StepLabelCallableInvalidReturnValueException;
use Craue\FormFlowBundle\Form\StepLabel;
use Craue\FormFlowBundle\Tests\UnitTestCase;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2024 Christian Raue
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
		return [
			['label'],
			['date'],
			[null],
		];
	}

	/**
	 * @dataProvider dataCreateStringLabel_invalidArgument
	 */
	public function testCreateStringLabel_invalidArgument($value) {
		$this->expectException(InvalidTypeException::class);

		StepLabel::createStringLabel($value);
	}

	public function dataCreateStringLabel_invalidArgument() {
		return [
			[true],
			[1.1],
			[function() { return 'country'; }],
		];
	}

	/**
	 * @dataProvider dataCreateCallableLabel
	 */
	public function testCreateCallableLabel($value, $expectedText) {
		$this->assertSame($expectedText, StepLabel::createCallableLabel($value)->getText());
	}

	public function dataCreateCallableLabel() {
		return [
			[self::class . '::_returnString', 'label'],
			[self::class . '::_returnNull', null],
			[function() { return 'label'; }, 'label'],
		];
	}

	/**
	 * @dataProvider dataCreateCallableLabel_invalidArgument
	 */
	public function testCreateCallableLabel_invalidArgument($value) {
		$this->expectException(\InvalidArgumentException::class);

		StepLabel::createCallableLabel($value)->getText();
	}

	public function dataCreateCallableLabel_invalidArgument() {
		return [
			['label'],
			['UnknownClass::unknownMethod'],
			[null],
		];
	}

	/**
	 * @dataProvider dataGetText_callableInvalidReturnValue
	 */
	public function testGetText_callableInvalidReturnValue($value) {
		$this->expectException(StepLabelCallableInvalidReturnValueException::class);

		StepLabel::createCallableLabel($value)->getText();
	}

	public function dataGetText_callableInvalidReturnValue() {
		return [
			[self::class . '::_returnOne'],
			[function() { return 1; }],
		];
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
