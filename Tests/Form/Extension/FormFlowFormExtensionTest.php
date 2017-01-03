<?php

namespace Craue\FormFlowBundle\Tests\Form\Extension;

use Craue\FormFlowBundle\Form\Extension\FormFlowFormExtension;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowFormExtensionTest extends \PHPUnit_Framework_TestCase {

	public function testGetExtendedType() {
		$extension = new FormFlowFormExtension();

		$useFqcn = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
		$this->assertSame($useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\FormType' : 'form', $extension->getExtendedType());
	}

}
