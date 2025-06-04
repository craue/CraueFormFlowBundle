<?php

namespace Craue\FormFlowBundle\Event;

use Craue\FormFlowBundle\Form\StepInterface;

/**
 * Is called once to define steps for the flow.
 *
 * @author Toni Uebernickel <tuebernickel@gmail.com>
 * @copyright 2011-2025 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class GetStepsEvent extends FormFlowEvent {

	/**
	 * @var StepInterface[]
	 */
	protected $steps = [];

	/**
	 * @param StepInterface[] $steps
	 */
	public function setSteps(array $steps) {
		$this->steps = $steps;
	}

	public function getSteps() {
		return $this->steps;
	}

}
