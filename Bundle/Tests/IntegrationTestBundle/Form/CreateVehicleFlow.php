<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
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
				'form_type' => 'createVehicle',
			),
			array(
				'label' => 'engine',
				'form_type' => 'createVehicle',
				'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
					return $estimatedCurrentStepNumber > 1 && !$flow->getFormData()->canHaveEngine();
				},
			),
			array(
				'label' => 'confirmation',
			),
		);
	}

}
