<?php

namespace Craue\FormFlowBundle\Tests\Exception;

use Craue\FormFlowBundle\Exception\InvalidTypeException;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class InvalidTypeExceptionTest extends \PHPUnit_Framework_TestCase {

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
		return array(
			array('Expected argument of type "A", but "string" given.', 'test', 'A'),
			array('Expected argument of either type "A" or "B", but "string" given.', 'test', array('A', 'B')),
			array('Expected argument of either type "A", "B", or "C", but "string" given.', 'test', array('A', 'B', 'C')),
			array('Expected argument of either type "A", "B", "C", or "D", but "string" given.', 'test', array('A', 'B', 'C', 'D')),
		);
	}

}
