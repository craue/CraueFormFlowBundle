<?php

namespace Craue\FormFlowBundle\Tests\Util;

use Craue\FormFlowBundle\Util\StringUtil;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class StringUtilTest extends \PHPUnit_Framework_TestCase {

	public function testGenerateRandomString() {
		$this->assertRegExp('/^[a-z0-9-]{1000}$/', StringUtil::generateRandomString(1000));
		$this->assertNotEquals(StringUtil::generateRandomString(10), StringUtil::generateRandomString(10));
	}

	/**
	 * @expectedException \Craue\FormFlowBundle\Exception\InvalidTypeException
	 */
	public function testGenerateRandomString_lengthNotInteger() {
		StringUtil::generateRandomString(null);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Length must be >= 0, "-1" given.
	 */
	public function testGenerateRandomString_lengthNegative() {
		StringUtil::generateRandomString(-1);
	}

	public function testIsRandomString() {
		$this->assertTrue(StringUtil::isRandomString('abcdefghijklmnopqrstuvwxyz0123456789-', 37));
		$this->assertTrue(StringUtil::isRandomString('', 0));
		$this->assertTrue(StringUtil::isRandomString('x', 1));

		// wrong length
		$this->assertFalse(StringUtil::isRandomString('', 1));
		$this->assertFalse(StringUtil::isRandomString(' ', 0));

		// wrong content
		$this->assertFalse(StringUtil::isRandomString('X', 1));
	}

	/**
	 * @expectedException \Craue\FormFlowBundle\Exception\InvalidTypeException
	 * @expectedExceptionMessage Expected argument of type "string", but "NULL" given.
	 */
	public function testIsRandomString_inputNotString() {
		StringUtil::isRandomString(null, 0);
	}

	/**
	 * @expectedException \Craue\FormFlowBundle\Exception\InvalidTypeException
	 * @expectedExceptionMessage Expected argument of type "integer", but "NULL" given.
	 */
	public function testIsRandomString_lengthNotInteger() {
		StringUtil::isRandomString('', null);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Length must be >= 0, "-1" given.
	 */
	public function testIsRandomString_lengthNegative() {
		StringUtil::isRandomString('', -1);
	}

	public function testIsRandomString_isCounterpartOf_generateRandomString() {
		$this->assertTrue(StringUtil::isRandomString(StringUtil::generateRandomString(1000), 1000));
	}

}
