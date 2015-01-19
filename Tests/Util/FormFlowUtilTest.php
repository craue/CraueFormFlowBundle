<?php

namespace Craue\FormFlowBundle\Tests\Util;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Util\FormFlowUtil;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowUtilTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var FormFlowUtil
	 */
	protected $util;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp() {
		$this->util = new FormFlowUtil();
	}

	public function testAddRouteParameters() {
		$flow = $this->getFlowWithMockedMethods(array('getName', 'loadStepsConfig'));

		$flow
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue(array(
				array(),
				array(),
			)))
		;

		$instanceId = 'xyz';
		$flow->setInstanceId($instanceId);

		$flow->nextStep();

		$actualParameters = $this->util->addRouteParameters(array('key' => 'value'), $flow);

		$this->assertEquals(array('key' => 'value', 'instance' => $instanceId, 'step' => 1), $actualParameters);
	}

	public function testAddRouteParameters_explicitStepNumber() {
		$flow = $this->getFlowWithMockedMethods(array('getName'));

		$instanceId = 'xyz';
		$flow->setInstanceId($instanceId);

		$actualParameters = $this->util->addRouteParameters(array('key' => 'value'), $flow, 5);

		$this->assertEquals(array('key' => 'value', 'instance' => $instanceId, 'step' => 5), $actualParameters);
	}

	public function testRemoveRouteParameters() {
		$flow = $this->getFlowWithMockedMethods(array('getName'));

		$actualParameters = $this->util->removeRouteParameters(array('key' => 'value', 'instance' => 'xyz', 'step' => 2), $flow);

		$this->assertEquals(array('key' => 'value'), $actualParameters);
	}

	/**
	 * @param string[] $methodNames Names of methods to be mocked.
	 * @return PHPUnit_Framework_MockObject_MockObject|FormFlow
	 */
	protected function getFlowWithMockedMethods(array $methodNames) {
		return $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', $methodNames);
	}

}
