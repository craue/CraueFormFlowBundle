<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2013 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class CreateVehicleFlow extends FormFlow {

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'createVehicle';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		return array(
			array(
				'label' => 'wheels',
				'type' => 'createVehicle',
			),
			array(
				'label' => 'engine',
				'type' => 'createVehicle',
				'skip' => function($currentStepNumber, $formData) {
					return $currentStepNumber > 1 && !$formData->canHaveEngine();
				},
			),
			array(
				'label' => 'confirmation',
				'type' => 'createVehicle', // needed to avoid InvalidOptionsException regarding option 'flowStep'
			),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFormOptions($step, array $options = array()) {
		$options = parent::getFormOptions($step, $options);

		$options['cascade_validation'] = true;
		$options['flowStep'] = $step;

		return $options;
	}

}
