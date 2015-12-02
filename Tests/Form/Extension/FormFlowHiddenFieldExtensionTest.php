<?php

namespace Craue\FormFlowBundle\Tests\Form\Extension;

use Craue\FormFlowBundle\Form\Extension\FormFlowHiddenFieldExtension;

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

		$useFqcn = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
		$this->assertSame($useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\HiddenType' : 'hidden', $extension->getExtendedType());
	}

}
