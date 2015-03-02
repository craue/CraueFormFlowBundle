<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Tests\UnitTestCase;

/**
 * Ensure that the methods for BC do what they should.
 *
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowBcMethodsTest extends UnitTestCase {

	public function testBcMethodDelegation_getCurrentStep() {
		$flow = $this->getFlowWithMockedDeprecationTriggerMethod('getCurrentStep', 'getCurrentStepNumber');

		$flow
			->expects($this->once())
			->method('getCurrentStepNumber')
			->will($this->returnValue(1))
		;

		$this->assertSame(1, $flow->getCurrentStep());
	}

	public function testBcMethodDelegation_getCurrentStepDescription() {
		$flow = $this->getFlowWithMockedDeprecationTriggerMethod('getCurrentStepDescription', 'getCurrentStepLabel');

		$flow
			->expects($this->once())
			->method('getCurrentStepLabel')
			->will($this->returnValue('summary'))
		;

		$this->assertSame('summary', $flow->getCurrentStepDescription());
	}

	public function testBcMethodDelegation_getMaxSteps() {
		$flow = $this->getFlowWithMockedDeprecationTriggerMethod('getMaxSteps', 'getStepCount');

		$flow
			->expects($this->once())
			->method('getStepCount')
			->will($this->returnValue(3))
		;

		$this->assertSame(3, $flow->getMaxSteps());
	}

	public function testBcMethodDelegation_getStepDescriptions() {
		$flow = $this->getFlowWithMockedDeprecationTriggerMethod('getStepDescriptions', 'getStepLabels');

		$flow
			->expects($this->once())
			->method('getStepLabels')
			->will($this->returnValue(array('step1', 'step2')))
		;

		$this->assertSame(array('step1', 'step2'), $flow->getStepDescriptions());
	}

	public function testBcMethodDelegation_getFirstStep() {
		$flow = $this->getFlowWithMockedDeprecationTriggerMethod('getFirstStep', 'getFirstStepNumber');

		$flow
			->expects($this->once())
			->method('getFirstStepNumber')
			->will($this->returnValue(2))
		;

		$this->assertSame(2, $flow->getFirstStep());
	}

	public function testBcMethodDelegation_getLastStep() {
		$flow = $this->getFlowWithMockedDeprecationTriggerMethod('getLastStep', 'getLastStepNumber');

		$flow
			->expects($this->once())
			->method('getLastStepNumber')
			->will($this->returnValue(5))
		;

		$this->assertSame(5, $flow->getLastStep());
	}

	public function testBcMethodDelegation_hasSkipStep() {
		$flow = $this->getFlowWithMockedDeprecationTriggerMethod('hasSkipStep', 'isStepSkipped');

		$flow
			->expects($this->once())
			->method('isStepSkipped')
			->will($this->returnValueMap(array(array(1, true))))
		;

		$this->assertTrue($flow->hasSkipStep(1));
	}

	/**
	 * @param string $bcMethodName Name of the method for BC.
	 * @param string $realMethodName Name of the new method actually being called.
	 * @return PHPUnit_Framework_MockObject_MockObject|FormFlow
	 */
	protected function getFlowWithMockedDeprecationTriggerMethod($bcMethodName, $realMethodName) {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'triggerDeprecationError', $realMethodName));

		$class = new \ReflectionClass($flow);
		foreach ($class->getMethods() as $method) {
			$methodName = $method->getName();

			switch ($methodName) {
				case 'triggerDeprecationError':
					$flow
						->expects($this->once())
						->method($methodName)
						->with(sprintf('Method Craue\FormFlowBundle\Form\FormFlow::%s is deprecated since version 2.0. Use method %s instead.', $bcMethodName, $realMethodName))
					;
					break;
				case $realMethodName:
					break;
				default:
					// assert that no other methods (of the flow class) are called
					$flow
						->expects($this->never())
						->method($methodName)
					;
					break;
			}
		}

		return $flow;
	}

}
