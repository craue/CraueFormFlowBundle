<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue64Flow extends FormFlow {

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'issue64';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		return array(
			array(
				'label' => 'step1',
				'form_type' => 'issue64',
			),
			array(
				'label' => 'step2',
				'form_type' => 'issue64',
			),
			array(
				'label' => 'step3',
				'form_type' => 'issue64',
			),
			array(
				'label' => 'step4',
				'form_type' => 'issue64',
			),
		);
	}

}
