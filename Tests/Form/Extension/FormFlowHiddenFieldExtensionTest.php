<?php

namespace Craue\FormFlowBundle\Tests\Form\Extension;

use Craue\FormFlowBundle\Form\Extension\FormFlowHiddenFieldExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowHiddenFieldExtensionTest extends TestCase {

	public function testGetExtendedType() {
		$extension = new FormFlowHiddenFieldExtension();

		$this->assertSame(HiddenType::class, $extension->getExtendedType());
		$this->assertSame([HiddenType::class], FormFlowHiddenFieldExtension::getExtendedTypes());
	}

}
