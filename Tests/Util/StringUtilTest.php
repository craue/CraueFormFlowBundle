<?php

namespace Craue\FormFlowBundle\Tests\Util;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Craue\FormFlowBundle\Util\StringUtil;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2022 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class StringUtilTest extends TestCase {

	public function testGenerateRandomString() {
		$this->assertEquals(1000, strlen(StringUtil::generateRandomString(1000)));
		$this->assertMatchesRegularExpression('/^[a-zA-Z0-9-_]{1000}$/', StringUtil::generateRandomString(1000));
		$this->assertNotEquals(StringUtil::generateRandomString(10), StringUtil::generateRandomString(10));
	}

	public function testGenerateRandomString_lengthNotInteger() {
		$this->expectException(InvalidTypeException::class);

		StringUtil::generateRandomString(null);
	}

	public function testGenerateRandomString_lengthNegative() {
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Length must be >= 0, "-1" given.');

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

	public function testIsRandomString_inputNotString() {
		$this->expectException(InvalidTypeException::class);
		$this->expectExceptionMessage('Expected argument of type "string", but "NULL" given.');

		StringUtil::isRandomString(null, 0);
	}

	public function testIsRandomString_lengthNotInteger() {
		$this->expectException(InvalidTypeException::class);
		$this->expectExceptionMessage('Expected argument of type "int", but "NULL" given.');

		StringUtil::isRandomString('', null);
	}

	public function testIsRandomString_lengthNegative() {
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Length must be >= 0, "-1" given.');

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
		return [
			['', null],
			['Flow', 'flow'],
			['Demo1', 'demo1'],
			['Demo1Flow', 'demo1'],
			['CreateLocation', 'createLocation'],
			['CreateLocationFlow', 'createLocation'],
			['MyCompany\MyBundle\Form\CreateLocation', 'createLocation'],
			['MyCompany\MyBundle\Form\CreateLocationFlow', 'createLocation'],
		];
	}

	public function testFqcnToFlowName_inputNotString() {
		$this->expectException(InvalidTypeException::class);
		$this->expectExceptionMessage('Expected argument of type "string", but "NULL" given.');

		StringUtil::fqcnToFlowName(null);
	}

}
