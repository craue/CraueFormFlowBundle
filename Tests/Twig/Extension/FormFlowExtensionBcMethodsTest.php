<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Tests\UnitTestCase;

/**
 * Tests for BC.
 *
 * @collectDeprecationNotices
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2016 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowExtensionBcMethodsTest extends UnitTestCase {

	private $deprecatedMessage = 'Twig filter %s is deprecated since version 3.0. Use filter %s instead.';

	public function testBcMethodDelegation_addDynamicStepNavigationParameter() {
		$extension = $this->getMockBuilder('\Craue\FormFlowBundle\Twig\Extension\FormFlowExtension')->setMethods(array('addDynamicStepNavigationParameters'))->getMock();

		$parameters = array('foo' => 'bar');
		$flow = $this->getMockedFlow();
		$stepNumber = 1;

		$extension
			->expects($this->once())
			->method('addDynamicStepNavigationParameters')
			->with($this->equalTo($parameters), $this->equalTo($flow), $this->equalTo($stepNumber))
		;

		$extension->addDynamicStepNavigationParameter($parameters, $flow, $stepNumber);
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'craue_addDynamicStepNavigationParameter', 'craue_addDynamicStepNavigationParameters')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_removeDynamicStepNavigationParameter() {
		$extension = $this->getMockBuilder('\Craue\FormFlowBundle\Twig\Extension\FormFlowExtension')->setMethods(array('removeDynamicStepNavigationParameters'))->getMock();

		$parameters = array('foo' => 'bar');
		$flow = $this->getMockedFlow();

		$extension
			->expects($this->once())
			->method('removeDynamicStepNavigationParameters')
			->with($this->equalTo($parameters), $this->equalTo($flow))
		;

		$extension->removeDynamicStepNavigationParameter($parameters, $flow);
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'craue_removeDynamicStepNavigationParameter', 'craue_removeDynamicStepNavigationParameters')), $this->getDeprecationNotices());
	}

}
