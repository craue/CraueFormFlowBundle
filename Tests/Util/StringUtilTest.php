<?php

namespace Craue\FormFlowBundle\Tests\Util;

use Craue\FormFlowBundle\Util\StringUtil;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class StringUtilTest extends TestCase {

	public function testGenerateRandomString() {
		$this->assertEquals(1000, strlen(StringUtil::generateRandomString(1000)));
		$this->assertRegExp('/^[a-zA-Z0-9-_]{1000}$/', StringUtil::generateRandomString(1000));
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
		$this->assertTrue(StringUtil::isRandomString('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_', 64));
		$this->assertTrue(StringUtil::isRandomString('', 0));
		$this->assertTrue(StringUtil::isRandomString('x', 1));

		// wrong length
		$this->assertFalse(StringUtil::isRandomString('', 1));
		$this->assertFalse(StringUtil::isRandomString(' ', 0));

		// wrong content
		$this->assertFalse(StringUtil::isRandomString('ä', 1));
		$this->assertFalse(StringUtil::isRandomString('=', 1));
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
	 * @expectedExceptionMessage Expected argument of type "int", but "NULL" given.
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

	/**
	 * @dataProvider dataFqcnToFlowName
	 */
	public function testFqcnToFlowName($fqcn, $expectedFlowName) {
		$this->assertSame($expectedFlowName, StringUtil::fqcnToFlowName($fqcn));
	}

	public function dataFqcnToFlowName() {
		return array(
			array(null, null),
			array('', null),
			array('Flow', 'flow'),
			array('Demo1', 'demo1'),
			array('Demo1Flow', 'demo1'),
			array('CreateLocation', 'createLocation'),
			array('CreateLocationFlow', 'createLocation'),
			array('MyCompany\MyBundle\Form\CreateLocation', 'createLocation'),
			array('MyCompany\MyBundle\Form\CreateLocationFlow', 'createLocation'),
		);
	}

}
