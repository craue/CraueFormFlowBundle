<?php

namespace Craue\FormFlowBundle\Event;

use Craue\FormFlowBundle\Form\FormFlowInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Is called once if an expired flow is detected.
 *
 * @author Tim Behrendsen <tim@siliconengine.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FlowExpiredEvent extends FormFlowEvent {

	/**
	 * @param FormFlowInterface $flow
	 * @param FormInterface $currentStepForm
	 */
	public function __construct(FormFlowInterface $flow, FormInterface $currentStepForm) {
		$this->flow = $flow;
		$this->currentStepForm = $currentStepForm;
	}

	/**
	 * @return FormInterface
	 */
	public function getCurrentStepForm() {
		return $this->currentStepForm;
	}

}
