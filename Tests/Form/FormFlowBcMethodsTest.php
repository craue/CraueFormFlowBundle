<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Tests\UnitTestCase;

/**
 * Tests for BC.
 *
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2016 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowBcMethodsTest extends UnitTestCase {

	protected $collectDeprecationNotices = true;

	private $deprecatedMessage = 'Method Craue\FormFlowBundle\Form\FormFlow::%s is deprecated since version 2.0. Use method %s instead.';

	public function testBcMethodDelegation_getCurrentStep() {
		$flowStub = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'getCurrentStepNumber'))->getMock();

		$flowStub
			->expects($this->once())
			->method('getCurrentStepNumber')
		;

		$flowStub->getCurrentStep();
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getCurrentStep', 'getCurrentStepNumber')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getCurrentStepDescription() {
		$flowStub = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'getCurrentStepLabel'))->getMock();

		$flowStub
			->expects($this->once())
			->method('getCurrentStepLabel')
		;

		$flowStub->getCurrentStepDescription();
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getCurrentStepDescription', 'getCurrentStepLabel')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getMaxSteps() {
		$flowStub = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'getStepCount'))->getMock();

		$flowStub
			->expects($this->once())
			->method('getStepCount')
		;

		$flowStub->getMaxSteps();
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getMaxSteps', 'getStepCount')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getStepDescriptions() {
		$flowStub = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'getStepLabels'))->getMock();

		$flowStub
			->expects($this->once())
			->method('getStepLabels')
		;

		$flowStub->getStepDescriptions();
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getStepDescriptions', 'getStepLabels')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getFirstStep() {
		$flowStub = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'getFirstStepNumber'))->getMock();

		$flowStub
			->expects($this->once())
			->method('getFirstStepNumber')
		;

		$flowStub->getFirstStep();
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getFirstStep', 'getFirstStepNumber')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getLastStep() {
		$flowStub = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'getLastStepNumber'))->getMock();

		$flowStub
			->expects($this->once())
			->method('getLastStepNumber')
		;

		$flowStub->getLastStep();
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getLastStep', 'getLastStepNumber')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_hasSkipStep() {
		$flowStub = $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods(array('getName', 'isStepSkipped'))->getMock();

		$stepNumber = 1;

		$flowStub
			->expects($this->once())
			->method('isStepSkipped')
			->with($this->equalTo($stepNumber))
		;

		$flowStub->hasSkipStep($stepNumber);
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'hasSkipStep', 'isStepSkipped')), $this->getDeprecationNotices());
	}

}
