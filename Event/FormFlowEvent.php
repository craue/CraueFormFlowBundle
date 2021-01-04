<?php

namespace Craue\FormFlowBundle\Event;

use Symfony\Component\EventDispatcher\Event as LegacyEvent;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Contracts\EventDispatcher\Event;

if (Kernel::VERSION_ID < 40300) {
	// TODO remove as soon as Symfony >= 4.3 is required
	/**
	 * @author Christian Raue <christian.raue@gmail.com>
	 * @copyright 2011-2021 Christian Raue
	 * @license http://opensource.org/licenses/mit-license.php MIT License
	 */
	abstract class FormFlowEvent extends LegacyEvent {
		use FormFlowEventTrait;
	}
} else {
	/**
	 * @author Christian Raue <christian.raue@gmail.com>
	 * @copyright 2011-2021 Christian Raue
	 * @license http://opensource.org/licenses/mit-license.php MIT License
	 */
	abstract class FormFlowEvent extends Event {
		use FormFlowEventTrait;
	}
}
