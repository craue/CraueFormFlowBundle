<?php

namespace Craue\FormFlowBundle\Event;

use Craue\FormFlowBundle\Form\FormFlowInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Is called once if revalidating previous steps failed.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PreviousStepInvalidEvent extends FormFlowEvent {

	/**
	 * @var int
	 */
	protected $invalidStepNumber;

	/**
	 * @var FormInterface
	 */
	protected $currentStepForm;

	/**
	 * @param FormFlowInterface $flow
	 * @param FormInterface $currentStepForm
	 * @param int $invalidStepNumber
	 */
	public function __construct(FormFlowInterface $flow, FormInterface $currentStepForm, $invalidStepNumber) {
		$this->flow = $flow;
		$this->currentStepForm = $currentStepForm;
		$this->invalidStepNumber = $invalidStepNumber;
	}

	/**
	 * @return FormInterface
	 */
	public function getCurrentStepForm() {
		return $this->currentStepForm;
	}

	/**
	 * @return int
	 */
	public function getInvalidStepNumber() {
		return $this->invalidStepNumber;
	}

}
