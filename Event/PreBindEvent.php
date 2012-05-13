<?php

namespace Craue\FormFlowBundle\Event;

use Craue\FormFlowBundle\Form\FormFlow;

/**
 * Is called once prior to binding any (neither saved nor request) data.
 * You can use this event to define steps to skip prior to determinating the current step, e.g. based on custom
 * session data.
 *
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2012 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class PreBindEvent extends FormFlowEvent {

	/**
	 * @param FormFlow $flow
	 */
	public function __construct(FormFlow $flow) {
		$this->flow = $flow;
	}

}
