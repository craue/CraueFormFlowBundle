<?php

namespace Craue\FormFlowBundle\Tests;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;
use Craue\FormFlowBundle\Form\StepInterface;
use Craue\FormFlowBundle\Storage\DataManagerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class UnitTestCase extends TestCase {

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|FormFlow
	 */
	protected function getMockedFlow() {
		return $this->getMockForAbstractClass('\Craue\FormFlowBundle\Form\FormFlow');
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|FormFlowInterface
	 */
	protected function getMockedFlowInterface() {
		// TODO replace by `$this->createMock('\Craue\FormFlowBundle\Form\FormFlowInterface')` as soon as PHPUnit >= 5.4 is required
		return $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlowInterface')->getMock();
	}

	/**
	 * @param string[] $methodNames Names of methods to be mocked.
	 * @return \PHPUnit_Framework_MockObject_MockObject|FormFlow
	 */
	protected function getFlowWithMockedMethods(array $methodNames) {
		return $this->getMockBuilder('\Craue\FormFlowBundle\Form\FormFlow')->setMethods($methodNames)->getMock();
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|StepInterface
	 */
	protected function getMockedStepInterface() {
		// TODO replace by `$this->createMock('\Craue\FormFlowBundle\Form\StepInterface')` as soon as PHPUnit >= 5.4 is required
		return $this->getMockBuilder('\Craue\FormFlowBundle\Form\StepInterface')->getMock();
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|DataManagerInterface
	 */
	protected function getMockedDataManagerInterface() {
		// TODO replace by `$this->createMock('\Craue\FormFlowBundle\Storage\DataManagerInterface')` as soon as PHPUnit >= 5.4 is required
		return $this->getMockBuilder('\Craue\FormFlowBundle\Storage\DataManagerInterface')->getMock();
	}

}
