<?php

namespace Craue\FormFlowBundle\Tests\Util;

use Craue\FormFlowBundle\Tests\UnitTestCase;
use Craue\FormFlowBundle\Util\FormFlowUtil;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowUtilTest extends UnitTestCase {

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
		$flow = $this->getFlowWithMockedMethods(array('loadStepsConfig'));

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
		$flow = $this->getMockedFlow();

		$instanceId = 'xyz';
		$flow->setInstanceId($instanceId);

		$actualParameters = $this->util->addRouteParameters(array('key' => 'value'), $flow, 5);

		$this->assertEquals(array('key' => 'value', 'instance' => $instanceId, 'step' => 5), $actualParameters);
	}

	public function testRemoveRouteParameters() {
		$flow = $this->getMockedFlow();

		$actualParameters = $this->util->removeRouteParameters(array('key' => 'value', 'instance' => 'xyz', 'step' => 2), $flow);

		$this->assertEquals(array('key' => 'value'), $actualParameters);
	}

}
