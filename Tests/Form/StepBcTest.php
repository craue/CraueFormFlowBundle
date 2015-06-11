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

	protected $collectDeprecationNotices = true;

	public function testCreateFromConfig_bcOptionType() {
		$step = Step::createFromConfig(1, array(
			'type' => 'myFormType',
		));

		$this->assertEquals(array('Step config option "type" is deprecated since version 3.0. Use "form_type" instead.'), $this->getDeprecationNotices());
		$this->assertEquals('myFormType', $step->getFormType());
	}

}
