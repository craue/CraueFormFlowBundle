<?php

namespace Craue\FormFlowBundle\Tests\Util;

use Craue\FormFlowBundle\Tests\UnitTestCase;
use Craue\FormFlowBundle\Util\FormFlowUtil;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
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
		$flow = $this->getFlowWithMockedMethods(['loadStepsConfig']);

		$flow
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue([
				[],
				[],
			]))
		;

		$instanceId = 'xyz';
		$flow->setInstanceId($instanceId);

		$flow->nextStep();

		$actualParameters = $this->util->addRouteParameters(['key' => 'value'], $flow);

		$this->assertEquals(['key' => 'value', 'instance' => $instanceId, 'step' => 1], $actualParameters);
	}

	public function testAddRouteParameters_explicitStepNumber() {
		$flow = $this->getMockedFlow();

		$instanceId = 'xyz';
		$flow->setInstanceId($instanceId);

		$actualParameters = $this->util->addRouteParameters(['key' => 'value'], $flow, 5);

		$this->assertEquals(['key' => 'value', 'instance' => $instanceId, 'step' => 5], $actualParameters);
	}

	public function testRemoveRouteParameters() {
		$flow = $this->getMockedFlow();

		$actualParameters = $this->util->removeRouteParameters(['key' => 'value', 'instance' => 'xyz', 'step' => 2], $flow);

		$this->assertEquals(['key' => 'value'], $actualParameters);
	}

}
