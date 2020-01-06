<?php

namespace Craue\FormFlowBundle\Tests\Twig\Extension;

use Craue\FormFlowBundle\Tests\UnitTestCase;
use Craue\FormFlowBundle\Twig\Extension\FormFlowExtension;

/**
 * Tests for BC.
 *
 * @group legacy
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowExtensionBcMethodsTest extends UnitTestCase {

	/**
	 * @expectedDeprecation Twig filter craue_addDynamicStepNavigationParameter is deprecated since CraueFormFlowBundle 3.0. Use filter craue_addDynamicStepNavigationParameters instead.
	 */
	public function testBcMethodDelegation_addDynamicStepNavigationParameter() {
		$extension = $this->getMockBuilder(FormFlowExtension::class)->setMethods(['addDynamicStepNavigationParameters'])->getMock();

		$parameters = ['foo' => 'bar'];
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
	 * @expectedDeprecation Twig filter craue_removeDynamicStepNavigationParameter is deprecated since CraueFormFlowBundle 3.0. Use filter craue_removeDynamicStepNavigationParameters instead.
	 */
	public function testBcMethodDelegation_removeDynamicStepNavigationParameter() {
		$extension = $this->getMockBuilder(FormFlowExtension::class)->setMethods(['removeDynamicStepNavigationParameters'])->getMock();

		$parameters = ['foo' => 'bar'];
		$flow = $this->getMockedFlow();

		$extension
			->expects($this->once())
			->method('removeDynamicStepNavigationParameters')
			->with($this->equalTo($parameters), $this->equalTo($flow))
		;

		$extension->removeDynamicStepNavigationParameter($parameters, $flow);
	}

}
