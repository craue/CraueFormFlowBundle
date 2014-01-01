<?php

namespace Craue\FormFlowBundle\Tests\Util;

use Craue\FormFlowBundle\Util\StringUtil;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class StringUtilTest extends \PHPUnit_Framework_TestCase {

	public function testGenerateRandomString() {
		$this->assertRegExp('/^[a-z0-9-]{10}$/', StringUtil::generateRandomString(10));
		$this->assertNotEquals(StringUtil::generateRandomString(10), StringUtil::generateRandomString(10));
	}

}
