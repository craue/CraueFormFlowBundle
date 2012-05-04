<?php

namespace Craue\FormFlowBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Is called once for the current step after validating the form data.
 *
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @copyright 2011-2012 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class PostValidateEvent extends Event {

	/**
	 * @var mixed
	 */
	private $formData;

	/**
	 * @param mixed $formData
	 */
	public function __construct($formData) {
		$this->formData = $formData;
	}

	/**
	 * @return mixed
	 */
	public function getFormData() {
		return $this->formData;
	}

}
