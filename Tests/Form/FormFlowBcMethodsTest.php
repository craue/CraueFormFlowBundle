<?php

namespace Craue\FormFlowBundle\Tests\Form;
use PHPUnit\Framework\TestCase;

/**
 * Tests for BC.
 *
 * @group legacy
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2018 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowBcMethodsTest extends TestCase {

	/**
	 * @expectedDeprecation Method Craue\FormFlowBundle\Form\FormFlow::getCurrentStep is deprecated since CraueFormFlowBundle 2.0. Use method getCurrentStepNumber instead.
	 */
	public function testBcMethodDelegation_getCurrentStep() {
		$flowStub = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'getCurrentStepNumber'))->getMock();

		$flowStub
			->expects($this->once())
			->method('getCurrentStepNumber')
		;

		$flowStub->getCurrentStep();
	}

	/**
	 * @expectedDeprecation Method Craue\FormFlowBundle\Form\FormFlow::getCurrentStepDescription is deprecated since CraueFormFlowBundle 2.0. Use method getCurrentStepLabel instead.
	 */
	public function testBcMethodDelegation_getCurrentStepDescription() {
		$flowStub = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'getCurrentStepLabel'))->getMock();

		$flowStub
			->expects($this->once())
			->method('getCurrentStepLabel')
		;

		$flowStub->getCurrentStepDescription();
	}

	/**
	 * @expectedDeprecation Method Craue\FormFlowBundle\Form\FormFlow::getMaxSteps is deprecated since CraueFormFlowBundle 2.0. Use method getStepCount instead.
	 */
	public function testBcMethodDelegation_getMaxSteps() {
		$flowStub = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'getStepCount'))->getMock();

		$flowStub
			->expects($this->once())
			->method('getStepCount')
		;

		$flowStub->getMaxSteps();
	}

	/**
	 * @expectedDeprecation Method Craue\FormFlowBundle\Form\FormFlow::getStepDescriptions is deprecated since CraueFormFlowBundle 2.0. Use method getStepLabels instead.
	 */
	public function testBcMethodDelegation_getStepDescriptions() {
		$flowStub = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'getStepLabels'))->getMock();

		$flowStub
			->expects($this->once())
			->method('getStepLabels')
		;

		$flowStub->getStepDescriptions();
	}

	/**
	 * @expectedDeprecation Method Craue\FormFlowBundle\Form\FormFlow::getFirstStep is deprecated since CraueFormFlowBundle 2.0. Use method getFirstStepNumber instead.
	 */
	public function testBcMethodDelegation_getFirstStep() {
		$flowStub = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'getFirstStepNumber'))->getMock();

		$flowStub
			->expects($this->once())
			->method('getFirstStepNumber')
		;

		$flowStub->getFirstStep();
	}

	/**
	 * @expectedDeprecation Method Craue\FormFlowBundle\Form\FormFlow::getLastStep is deprecated since CraueFormFlowBundle 2.0. Use method getLastStepNumber instead.
	 */
	public function testBcMethodDelegation_getLastStep() {
		$flowStub = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'getLastStepNumber'))->getMock();

		$flowStub
			->expects($this->once())
			->method('getLastStepNumber')
		;

		$flowStub->getLastStep();
	}

	/**
	 * @expectedDeprecation Method Craue\FormFlowBundle\Form\FormFlow::hasSkipStep is deprecated since CraueFormFlowBundle 2.0. Use method isStepSkipped instead.
	 */
	public function testBcMethodDelegation_hasSkipStep() {
		$flowStub = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'isStepSkipped'))->getMock();

		$stepNumber = 1;

		$flowStub
			->expects($this->once())
			->method('isStepSkipped')
			->with($this->equalTo($stepNumber))
		;

		$flowStub->hasSkipStep($stepNumber);
	}

}
