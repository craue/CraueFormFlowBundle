<?php

namespace Craue\FormFlowBundle\Tests\Form;

/**
 * Ensure that the methods for BC do what they should.
 *
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowBcMethodsTest extends \PHPUnit_Framework_TestCase {

	public function testBcMethodDelegation_getCurrentStep() {
		$flowStub = $this->getFlowWithMockedDeprecationTriggerMethod('getCurrentStep', 'getCurrentStepNumber');

		$flowStub
			->expects($this->once())
			->method('getCurrentStepNumber')
			->will($this->returnValue(1))
		;

		$this->assertSame(1, $flowStub->getCurrentStep());
	}

	public function testBcMethodDelegation_getCurrentStepDescription() {
		$flowStub = $this->getFlowWithMockedDeprecationTriggerMethod('getCurrentStepDescription', 'getCurrentStepLabel');

		$flowStub
			->expects($this->once())
			->method('getCurrentStepLabel')
			->will($this->returnValue('summary'))
		;

		$this->assertSame('summary', $flowStub->getCurrentStepDescription());
	}

	public function testBcMethodDelegation_getMaxSteps() {
		$flowStub = $this->getFlowWithMockedDeprecationTriggerMethod('getMaxSteps', 'getStepCount');

		$flowStub
			->expects($this->once())
			->method('getStepCount')
			->will($this->returnValue(3))
		;

		$this->assertSame(3, $flowStub->getMaxSteps());
	}

	public function testBcMethodDelegation_getStepDescriptions() {
		$flowStub = $this->getFlowWithMockedDeprecationTriggerMethod('getStepDescriptions', 'getStepLabels');

		$flowStub
			->expects($this->once())
			->method('getStepLabels')
			->will($this->returnValue(array('step1', 'step2')))
		;

		$this->assertSame(array('step1', 'step2'), $flowStub->getStepDescriptions());
	}

	public function testBcMethodDelegation_getFirstStep() {
		$flowStub = $this->getFlowWithMockedDeprecationTriggerMethod('getFirstStep', 'getFirstStepNumber');

		$flowStub
			->expects($this->once())
			->method('getFirstStepNumber')
			->will($this->returnValue(2))
		;

		$this->assertSame(2, $flowStub->getFirstStep());
	}

	public function testBcMethodDelegation_getLastStep() {
		$flowStub = $this->getFlowWithMockedDeprecationTriggerMethod('getLastStep', 'getLastStepNumber');

		$flowStub
			->expects($this->once())
			->method('getLastStepNumber')
			->will($this->returnValue(5))
		;

		$this->assertSame(5, $flowStub->getLastStep());
	}

	public function testBcMethodDelegation_hasSkipStep() {
		$flowStub = $this->getFlowWithMockedDeprecationTriggerMethod('hasSkipStep', 'isStepSkipped');

		$flowStub
			->expects($this->once())
			->method('isStepSkipped')
			->will($this->returnValueMap(array(array(1, true))))
		;

		$this->assertTrue($flowStub->hasSkipStep(1));
	}

	/**
	 * @param string $bcMethodName Name of the method for BC.
	 * @param string $realMethodName Name of the new method actually being called.
	 * @return PHPUnit_Framework_MockObject_MockObject|\Craue\FormFlowBundle\Form\FormFlow
	 */
	protected function getFlowWithMockedDeprecationTriggerMethod($bcMethodName, $realMethodName) {
		$flowStub = $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', array('getName', 'triggerDeprecationError', $realMethodName));

		$class = new \ReflectionClass($flowStub);
		foreach ($class->getMethods() as $method) {
			$methodName = $method->getName();

			switch ($methodName) {
				case 'triggerDeprecationError':
					$flowStub
						->expects($this->once())
						->method($methodName)
						->with(sprintf('%s() is deprecated since version 2.0. Use %s() instead.', $bcMethodName, $realMethodName))
					;
					break;
				case $realMethodName:
					break;
				default:
					// assert that no other methods (of the flow class) are called
					$flowStub
						->expects($this->never())
						->method($methodName)
					;
					break;
			}
		}

		return $flowStub;
	}

}
