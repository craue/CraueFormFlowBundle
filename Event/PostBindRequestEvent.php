<?php

namespace Craue\FormFlowBundle\Event;

use Craue\FormFlowBundle\Form\FormFlowInterface;

/**
 * Is called once for the current step after binding the request.
 *
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PostBindRequestEvent extends FormFlowEvent {

	/**
	 * @var mixed
	 */
	protected $formData;

	/**
	 * @var integer
	 */
	protected $step;

	/**
	 * @param FormFlowInterface $flow
	 * @param mixed $formData
	 * @param integer $step
	 */
	public function __construct(FormFlowInterface $flow, $formData, $step) {
		$this->flow = $flow;
		$this->formData = $formData;
		$this->step = $step;
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
	public function getStep() {
		return $this->step;
	}

}
