<?php

namespace Craue\FormFlowBundle\Event;

use Craue\FormFlowBundle\Form\FormFlow;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2012 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
abstract class FormFlowEvent extends Event {

	/**
	 * @var FormFlow
	 */
	protected $flow;

	/**
	 * @return FormFlow
	 */
	public function getFlow() {
		return $this->flow;
	}

}
