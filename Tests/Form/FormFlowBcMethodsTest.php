<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Tests\UnitTestCase;

/**
 * Tests for BC.
 *
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowBcMethodsTest extends UnitTestCase {

	protected $collectDeprecationNotices = true;

	private $deprecatedMessage = 'Method Craue\FormFlowBundle\Form\FormFlow::%s is deprecated since version 2.0. Use method %s instead.';

	public function testBcMethodDelegation_getCurrentStep() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'getCurrentStepNumber'));

		$flow
			->expects($this->once())
			->method('getCurrentStepNumber')
		;

		$flow->getCurrentStep();
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getCurrentStep', 'getCurrentStepNumber')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getCurrentStepDescription() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'getCurrentStepLabel'));

		$flow
			->expects($this->once())
			->method('getCurrentStepLabel')
		;

		$flow->getCurrentStepDescription();
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getCurrentStepDescription', 'getCurrentStepLabel')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getMaxSteps() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'getStepCount'));

		$flow
			->expects($this->once())
			->method('getStepCount')
		;

		$flow->getMaxSteps();
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getMaxSteps', 'getStepCount')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getStepDescriptions() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'getStepLabels'));

		$flow
			->expects($this->once())
			->method('getStepLabels')
		;

		$flow->getStepDescriptions();
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getStepDescriptions', 'getStepLabels')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getFirstStep() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'getFirstStepNumber'));

		$flow
			->expects($this->once())
			->method('getFirstStepNumber')
		;

		$flow->getFirstStep();
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getFirstStep', 'getFirstStepNumber')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getLastStep() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'getLastStepNumber'));

		$flow
			->expects($this->once())
			->method('getLastStepNumber')
		;

		$flow->getLastStep();
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getLastStep', 'getLastStepNumber')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_hasSkipStep() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'isStepSkipped'));

		$stepNumber = 1;

		$flow
			->expects($this->once())
			->method('isStepSkipped')
			->with($this->equalTo($stepNumber))
		;

		$flow->hasSkipStep($stepNumber);
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'hasSkipStep', 'isStepSkipped')), $this->getDeprecationNotices());
	}

}
