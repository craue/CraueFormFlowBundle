<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Tests\UnitTestCase;

/**
 * Tests for BC.
 *
 * @group legacy
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowExtensionBcMethodsTest extends UnitTestCase {

	/**
	 * @expectedDeprecation Twig filter craue_addDynamicStepNavigationParameter is deprecated since version 3.0. Use filter craue_addDynamicStepNavigationParameters instead.
	 */
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
	}

	/**
	 * @expectedDeprecation Twig filter craue_removeDynamicStepNavigationParameter is deprecated since version 3.0. Use filter craue_removeDynamicStepNavigationParameters instead.
	 */
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
	}

}
