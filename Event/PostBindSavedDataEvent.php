<?php

namespace Craue\FormFlowBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Is called for each step after binding its saved form data.
 *
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @copyright 2011-2012 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class PostBindSavedDataEvent extends Event {

	/**
	 * @var array
	 */
	private $formData;

	/**
	 * @var integer
	 */
	private $step;

	/**
	 * @param array $formData
	 * @param integer $step
	 */
	public function __construct($formData, $step) {
		$this->formData = $formData;
		$this->step = $step;
	}

	/**
	 * @return array
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
