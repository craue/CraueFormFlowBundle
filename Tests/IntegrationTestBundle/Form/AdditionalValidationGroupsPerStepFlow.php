<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class AdditionalValidationGroupsPerStepFlow extends FormFlow {

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'additionalValidationGroupsPerStep';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		return array(
			array(
				'label' => 'no group',
				'type' => new AdditionalValidationGroupsPerStepForm(),
				'additional_validation_groups' => null,
			),
			array(
				'label' => 'one group',
				'type' => new AdditionalValidationGroupsPerStepForm(),
				'additional_validation_groups' => 'additionalValidationGroupsPerStep2',
			),
			array(
				'label' => 'two groups',
				'type' => new AdditionalValidationGroupsPerStepForm(),
				'additional_validation_groups' => array('additionalValidationGroupsPerStep3a', 'additionalValidationGroupsPerStep3b'),
			),
			array(
				'label' => 'confirmation',
			),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFormOptions($step, array $options = array()) {
		$options = parent::getFormOptions($step, $options);

		$options['cascade_validation'] = true;

		return $options;
	}

}
