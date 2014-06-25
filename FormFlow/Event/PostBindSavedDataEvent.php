<?php

namespace Craue\FormFlowBundle\FormFlow\Event;

use Craue\FormFlowBundle\FormFlow\FormFlowInterface;

/**
 * Is called for each step after binding its saved form data.
 *
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PostBindSavedDataEvent extends FormFlowEvent {

	/**
	 * @var mixed
	 */
	protected $formData;

	/**
	 * @var integer
	 */
	protected $stepNumber;

	/**
	 * @param FormFlowInterface $flow
	 * @param mixed $formData
	 * @param integer $stepNumber
	 */
	public function __construct(FormFlowInterface $flow, $formData, $stepNumber) {
		$this->flow = $flow;
		$this->formData = $formData;
		$this->stepNumber = $stepNumber;
	}

	/**
	 * @return mixed
	 */
	public function getFormData() {
		return $this->formData;
	}

	/**
	 * @return integer
	 */
	public function getStepNumber() {
		return $this->stepNumber;
	}

}
