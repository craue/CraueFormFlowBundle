<?php

namespace Craue\FormFlowBundle\Tests\Util;

use Craue\FormFlowBundle\Util\DataTypeUtil;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class DataTypeUtilTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider dataIsStringArray_validValues
	 */
	public function testIsStringArray_validValues($value) {
		$this->assertTrue(DataTypeUtil::isStringArray($value));
	}

	public function dataIsStringArray_validValues() {
		return array(
			array(array()),
			array(array('myGroup1', 'myGroup2')),
		);
	}

	/**
	 * @dataProvider dataIsStringArray_invalidValues
	 */
	public function testIsStringArray_invalidValues($value) {
		$this->assertFalse(DataTypeUtil::isStringArray($value));
	}

	public function dataIsStringArray_invalidValues() {
		return array(
			array(123),
			array(true),
			array(false),
			array('myGroup1'),
			array(array('myGroup', null)),
			array(array('myGroup', array())),
			array(new \stdClass()),
			array(array('myGroup', new \stdClass())),
		);
	}

}
