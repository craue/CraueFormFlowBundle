<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CreateTopicFlow extends FormFlow {

	protected $allowDynamicStepNavigation = true;

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		$useFqcn = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
		$formType = $useFqcn ? 'Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\CreateTopicForm' : 'createTopic';

		return array(
			array(
				'label' => 'basics',
				'form_type' => $formType,
			),
			array(
				'label' => 'comment',
				'form_type' => $formType,
			),
			array(
				'label' => 'bug_details',
				'form_type' => $formType,
				'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
					return $estimatedCurrentStepNumber > 1 && !$flow->getFormData()->isBugReport();
				},
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

		if ($step === 3) {
			$options['isBugReport'] = $this->getFormData()->isBugReport();
		}

		return $options;
	}

}
