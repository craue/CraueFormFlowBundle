<?php

namespace Craue\FormFlowBundle\Tests\FormFlow;

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

	private $deprecatedMessage = 'Method Craue\FormFlowBundle\FormFlow\FormFlow::%s is deprecated since version 2.0. Use method %s instead.';

	public function testBcMethodDelegation_getCurrentStep() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'getCurrentStepNumber'));

		$flow
			->expects($this->once())
			->method('getCurrentStepNumber')
			->will($this->returnValue(1))
		;

		$this->assertSame(1, $flow->getCurrentStep());
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getCurrentStep', 'getCurrentStepNumber')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getCurrentStepDescription() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'getCurrentStepLabel'));

		$flow
			->expects($this->once())
			->method('getCurrentStepLabel')
			->will($this->returnValue('summary'))
		;

		$this->assertSame('summary', $flow->getCurrentStepDescription());
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getCurrentStepDescription', 'getCurrentStepLabel')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getMaxSteps() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'getStepCount'));

		$flow
			->expects($this->once())
			->method('getStepCount')
			->will($this->returnValue(3))
		;

		$this->assertSame(3, $flow->getMaxSteps());
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getMaxSteps', 'getStepCount')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getStepDescriptions() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'getStepLabels'));

		$flow
			->expects($this->once())
			->method('getStepLabels')
			->will($this->returnValue(array('step1', 'step2')))
		;

		$this->assertSame(array('step1', 'step2'), $flow->getStepDescriptions());
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getStepDescriptions', 'getStepLabels')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getFirstStep() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'getFirstStepNumber'));

		$flow
			->expects($this->once())
			->method('getFirstStepNumber')
			->will($this->returnValue(2))
		;

		$this->assertSame(2, $flow->getFirstStep());
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getFirstStep', 'getFirstStepNumber')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_getLastStep() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'getLastStepNumber'));

		$flow
			->expects($this->once())
			->method('getLastStepNumber')
			->will($this->returnValue(5))
		;

		$this->assertSame(5, $flow->getLastStep());
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'getLastStep', 'getLastStepNumber')), $this->getDeprecationNotices());
	}

	public function testBcMethodDelegation_hasSkipStep() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'isStepSkipped'));

		$flow
			->expects($this->once())
			->method('isStepSkipped')
			->will($this->returnValueMap(array(array(1, true))))
		;

		$this->assertTrue($flow->hasSkipStep(1));
		$this->assertEquals(array(sprintf($this->deprecatedMessage, 'hasSkipStep', 'isStepSkipped')), $this->getDeprecationNotices());
	}

}
