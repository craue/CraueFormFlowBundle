<?php

namespace Craue\FormFlowBundle\Event;

use Craue\FormFlowBundle\Form\FormFlowInterface;
use Symfony\Component\EventDispatcher\Event as LegacyEvent;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Contracts\EventDispatcher\Event;

if (Kernel::VERSION_ID < 40300) {
	// TODO remove as soon as Symfony >= 4.3 is required
	/**
	 * @author Christian Raue <christian.raue@gmail.com>
	 * @copyright 2011-2020 Christian Raue
	 * @license http://opensource.org/licenses/mit-license.php MIT License
	 */
	abstract class FormFlowEvent extends LegacyEvent {
		use _FormFlowEventTrait;
	}
} else {
	/**
	 * @author Christian Raue <christian.raue@gmail.com>
	 * @copyright 2011-2020 Christian Raue
	 * @license http://opensource.org/licenses/mit-license.php MIT License
	 */
	abstract class FormFlowEvent extends Event {
		use _FormFlowEventTrait;
	}
}

trait _FormFlowEventTrait {

	/**
	 * @var FormFlowInterface
	 */
	protected $flow;

	/**
	 * @param FormFlowInterface $flow
	 */
	public function __construct(FormFlowInterface $flow) {
		$this->flow = $flow;
	}

	/**
	 * @return FormFlowInterface
	 */
	public function getFlow() {
		return $this->flow;
	}

}
