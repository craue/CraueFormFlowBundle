<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue149Flow extends FormFlow {

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		$formType = Issue149Form::class;

		return [
			[
				'label' => 'step1',
				'form_type' => $formType,
			],
			[
				'label' => 'step2',
				'form_type' => $formType,
			],
			[
				'label' => 'step3',
				'form_type' => $formType,
			],
		];
	}

}
