<?php

namespace Craue\FormFlowBundle\Event;

use Craue\FormFlowBundle\Form\FormFlow;

/**
 * Is called once for the current step after validating the form data.
 *
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2012 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class PostValidateEvent extends FormFlowEvent {

	/**
	 * @var mixed
	 */
	protected $formData;

	/**
	 * @param FormFlow $flow
	 * @param mixed $formData
	 */
	public function __construct(FormFlow $flow, $formData) {
		$this->flow = $flow;
		$this->formData = $formData;
	}

	/**
	 * @return mixed
	 */
	public function getFormData() {
		return $this->formData;
	}

}
