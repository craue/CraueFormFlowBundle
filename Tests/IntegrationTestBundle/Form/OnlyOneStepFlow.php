<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class OnlyOneStepFlow extends FormFlow {

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'onlyOneStep';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		return array(
			array(
				'label' => 'single step',
			),
		);
	}

}
