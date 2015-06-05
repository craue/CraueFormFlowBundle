<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Form\Step;
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
class StepBcTest extends UnitTestCase {

	/**
	 * @var string[]
	 */
	private $deprecationNotices;

	protected function setUp() {
		$this->deprecationNotices = array();

		$that = $this;
		set_error_handler(function($errno, $errstr, $errfile, $errline) use ($that) {
			if ($errno === E_USER_DEPRECATED) {
				$that->addDepreactionNotice($errstr);
			}
		});
	}

	protected function tearDown() {
		restore_error_handler();
	}

	public function addDepreactionNotice($notice) {
		$this->deprecationNotices[] = $notice;
	}

	public function testCreateFromConfig_bcOptionType() {
		$step = Step::createFromConfig(1, array(
			'type' => 'myFormType',
		));

		$this->assertEquals(array('Step config option "type" is deprecated since version 3.0. Use "form_type" instead.'), $this->deprecationNotices);
		$this->assertEquals('myFormType', $step->getFormType());
	}

}
