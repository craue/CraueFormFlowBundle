<?php

namespace Craue\FormFlowBundle\Tests\FormFlow\Extension;

use Craue\FormFlowBundle\FormFlow\Extension\FormFlowFormExtension;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowFormExtensionTest extends \PHPUnit_Framework_TestCase {

	public function testGetExtendedType() {
		$extension = new FormFlowFormExtension();

		$this->assertSame('form', $extension->getExtendedType());
	}

}
