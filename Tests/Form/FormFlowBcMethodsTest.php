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
class FormFlowBcMethodsTest extends UnitTestCase {

	/**
	 * @expectedDeprecation Method Craue\FormFlowBundle\Form\FormFlow::getCurrentStep is deprecated since version 2.0. Use method getCurrentStepNumber instead.
	 */
	public function testBcMethodDelegation_getCurrentStep() {
		$flow = $this->getFlowWithMockedMethods(array('getCurrentStepNumber'));

		$flow
			->expects($this->once())
			->method('getCurrentStepNumber')
		;

		$flow->getCurrentStep();
	}

	/**
	 * @expectedDeprecation Method Craue\FormFlowBundle\Form\FormFlow::getCurrentStepDescription is deprecated since version 2.0. Use method getCurrentStepLabel instead.
	 */
	public function testBcMethodDelegation_getCurrentStepDescription() {
		$flow = $this->getFlowWithMockedMethods(array('getCurrentStepLabel'));

		$flow
			->expects($this->once())
			->method('getCurrentStepLabel')
		;

		$flow->getCurrentStepDescription();
	}

	/**
	 * @expectedDeprecation Method Craue\FormFlowBundle\Form\FormFlow::getMaxSteps is deprecated since version 2.0. Use method getStepCount instead.
	 */
	public function testBcMethodDelegation_getMaxSteps() {
		$flow = $this->getFlowWithMockedMethods(array('getStepCount'));

		$flow
			->expects($this->once())
			->method('getStepCount')
		;

		$flow->getMaxSteps();
	}

	/**
	 * @expectedDeprecation Method Craue\FormFlowBundle\Form\FormFlow::getStepDescriptions is deprecated since version 2.0. Use method getStepLabels instead.
	 */
	public function testBcMethodDelegation_getStepDescriptions() {
		$flow = $this->getFlowWithMockedMethods(array('getStepLabels'));

		$flow
			->expects($this->once())
			->method('getStepLabels')
		;

		$flow->getStepDescriptions();
	}

	/**
	 * @expectedDeprecation Method Craue\FormFlowBundle\Form\FormFlow::getFirstStep is deprecated since version 2.0. Use method getFirstStepNumber instead.
	 */
	public function testBcMethodDelegation_getFirstStep() {
		$flow = $this->getFlowWithMockedMethods(array('getFirstStepNumber'));

		$flow
			->expects($this->once())
			->method('getFirstStepNumber')
		;

		$flow->getFirstStep();
	}

	/**
	 * @expectedDeprecation Method Craue\FormFlowBundle\Form\FormFlow::getLastStep is deprecated since version 2.0. Use method getLastStepNumber instead.
	 */
	public function testBcMethodDelegation_getLastStep() {
		$flow = $this->getFlowWithMockedMethods(array('getLastStepNumber'));

		$flow
			->expects($this->once())
			->method('getLastStepNumber')
		;

		$flow->getLastStep();
	}

	/**
	 * @expectedDeprecation Method Craue\FormFlowBundle\Form\FormFlow::hasSkipStep is deprecated since version 2.0. Use method isStepSkipped instead.
	 */
	public function testBcMethodDelegation_hasSkipStep() {
		$flow = $this->getFlowWithMockedMethods(array('isStepSkipped'));

		$stepNumber = 1;

		$flow
			->expects($this->once())
			->method('isStepSkipped')
			->with($this->equalTo($stepNumber))
		;

		$flow->hasSkipStep($stepNumber);
	}

}
