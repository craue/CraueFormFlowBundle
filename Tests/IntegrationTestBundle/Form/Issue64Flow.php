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
		$useFqcn = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
		$formType = $useFqcn ? 'Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\Issue64Form' : 'issue64';

		return array(
			array(
				'label' => 'step1',
				'type' => $formType,
			),
			array(
				'label' => 'step2',
				'type' => $formType,
			),
			array(
				'label' => 'step3',
				'type' => $formType,
			),
			array(
				'label' => 'step4',
				'type' => $formType,
			),
		);
	}

}
