<?php

namespace Craue\FormFlowBundle\Tests\FormFlow\Extension;

use Craue\FormFlowBundle\FormFlow\Extension\FormFlowHiddenFieldExtension;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowHiddenFieldExtensionTest extends \PHPUnit_Framework_TestCase {

	public function testGetExtendedType() {
		$extension = new FormFlowHiddenFieldExtension();

		$this->assertSame('hidden', $extension->getExtendedType());
	}

}
