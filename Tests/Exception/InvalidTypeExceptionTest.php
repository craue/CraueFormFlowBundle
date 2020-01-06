<?php

namespace Craue\FormFlowBundle\Tests\Exception;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use PHPUnit\Framework\TestCase;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class InvalidTypeExceptionTest extends TestCase {

	/**
	 * @dataProvider dataExceptionMessage
	 */
	public function testExceptionMessage($expectedMessage, $value, $allowedType) {
		try {
			throw new InvalidTypeException($value, $allowedType);
		} catch (InvalidTypeException $e) {
			$this->assertSame($expectedMessage, $e->getMessage());
		}
	}

	public function dataExceptionMessage() {
		return [
			['Expected argument of type "A", but "string" given.', 'test', 'A'],
			['Expected argument of either type "A" or "B", but "string" given.', 'test', ['A', 'B']],
			['Expected argument of either type "A", "B", or "C", but "string" given.', 'test', ['A', 'B', 'C']],
			['Expected argument of either type "A", "B", "C", or "D", but "string" given.', 'test', ['A', 'B', 'C', 'D']],
		];
	}

}
