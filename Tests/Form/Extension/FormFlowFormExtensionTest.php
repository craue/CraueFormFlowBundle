<?php

namespace Craue\FormFlowBundle\Tests\Form\Extension;

use Craue\FormFlowBundle\Form\Extension\FormFlowFormExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowFormExtensionTest extends TestCase {

	public function testGetExtendedType() {
		$extension = new FormFlowFormExtension();

		$this->assertSame(FormType::class, $extension->getExtendedType());
		$this->assertSame([FormType::class], FormFlowFormExtension::getExtendedTypes());
	}

}
