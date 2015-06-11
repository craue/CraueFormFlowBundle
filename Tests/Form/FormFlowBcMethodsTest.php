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
		$flowStub = $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', array('getName', 'getCurrentStepNumber'));

		$flowStub
			->expects($this->once())
			->method('getCurrentStepNumber')
			->will($this->returnValue(1))
		;

		$this->assertSame(1, $flowStub->getCurrentStep());
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getCurrentStep', 'getCurrentStepNumber')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getCurrentStepDescription() {
		$flowStub = $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', array('getName', 'getCurrentStepLabel'));

		$flowStub
			->expects($this->once())
			->method('getCurrentStepLabel')
			->will($this->returnValue('summary'))
		;

		$this->assertSame('summary', $flowStub->getCurrentStepDescription());
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getCurrentStepDescription', 'getCurrentStepLabel')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getMaxSteps() {
		$flowStub = $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', array('getName', 'getStepCount'));

		$flowStub
			->expects($this->once())
			->method('getStepCount')
			->will($this->returnValue(3))
		;

		$this->assertSame(3, $flowStub->getMaxSteps());
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getMaxSteps', 'getStepCount')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getStepDescriptions() {
		$flowStub = $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', array('getName', 'getStepLabels'));

		$flowStub
			->expects($this->once())
			->method('getStepLabels')
			->will($this->returnValue(array('step1', 'step2')))
		;

		$this->assertSame(array('step1', 'step2'), $flowStub->getStepDescriptions());
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getStepDescriptions', 'getStepLabels')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getFirstStep() {
		$flowStub = $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', array('getName', 'getFirstStepNumber'));

		$flowStub
			->expects($this->once())
			->method('getFirstStepNumber')
			->will($this->returnValue(2))
		;

		$this->assertSame(2, $flowStub->getFirstStep());
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getFirstStep', 'getFirstStepNumber')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getLastStep() {
		$flowStub = $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', array('getName', 'getLastStepNumber'));

		$flowStub
			->expects($this->once())
			->method('getLastStepNumber')
			->will($this->returnValue(5))
		;

		$this->assertSame(5, $flowStub->getLastStep());
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getLastStep', 'getLastStepNumber')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_hasSkipStep() {
		$flowStub = $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', array('getName', 'isStepSkipped'));

		$flowStub
			->expects($this->once())
			->method('isStepSkipped')
			->will($this->returnValueMap(array(array(1, true))))
		;

		$this->assertTrue($flowStub->hasSkipStep(1));
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'hasSkipStep', 'isStepSkipped')), $this->getDeprecationNotices());
	}

}
