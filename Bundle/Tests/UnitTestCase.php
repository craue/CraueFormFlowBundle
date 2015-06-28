<?php

namespace Craue\FormFlowBundle\Tests;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;
use Craue\FormFlowBundle\Form\StepInterface;
use Craue\FormFlowBundle\Storage\DataManagerInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class UnitTestCase extends \PHPUnit_Framework_TestCase {

	/**
	 * @var boolean If deprecation notices triggered during tests will be collected.
	 */
	protected $collectDeprecationNotices = false;

	/**
	 * @var string[]
	 */
	private $deprecationNotices;

	protected function setUp() {
		$this->deprecationNotices = array();

		if ($this->collectDeprecationNotices) {
			$that = $this;
			set_error_handler(function($errno, $errstr, $errfile, $errline) use ($that) {
				if ($errno === E_USER_DEPRECATED) {
					$that->addDeprecationNotice($errstr);
				}
			});
		}
	}

	protected function tearDown() {
		if ($this->collectDeprecationNotices) {
			restore_error_handler();
		}
	}

	// needs to be public for PHP 5.3 compatibility
	public function addDeprecationNotice($notice) {
		$this->deprecationNotices[] = $notice;
	}

	protected function getDeprecationNotices() {
		return $this->deprecationNotices;
	}

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
		return $this->getMock('\Craue\FormFlowBundle\Form\FormFlowInterface');
	}

	/**
	 * @param string[] $methodNames Names of methods to be mocked.
	 * @return PHPUnit_Framework_MockObject_MockObject|FormFlow
	 */
	protected function getFlowWithMockedMethods(array $methodNames) {
		return $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', $methodNames);
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|StepInterface
	 */
	protected function getMockedStepInterface() {
		return $this->getMock('\Craue\FormFlowBundle\Form\StepInterface');
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|DataManagerInterface
	 */
	protected function getMockedDataManagerInterface() {
		return $this->getMock('\Craue\FormFlowBundle\Storage\DataManagerInterface');
	}

}
