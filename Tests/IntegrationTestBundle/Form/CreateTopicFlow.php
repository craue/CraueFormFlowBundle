<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CreateTopicFlow extends FormFlow {

	protected $allowDynamicStepNavigation = true;

	/**
	 * @var FormTypeInterface
	 */
	protected $formType;

	/**
	 * @param FormTypeInterface $formType
	 */
	public function setFormType(FormTypeInterface $formType) {
		$this->formType = $formType;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'createTopic';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		return array(
			array(
				'label' => 'basics',
				'form_type' => $this->formType,
			),
			array(
				'label' => 'comment',
				'form_type' => $this->formType,
			),
			array(
				'label' => 'bug_details',
				'form_type' => $this->formType,
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

		$options['cascade_validation'] = true;

		if ($step === 3) {
			$options['isBugReport'] = $this->getFormData()->isBugReport();
		}

		return $options;
	}

}
