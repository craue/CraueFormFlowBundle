<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Event\PreviousStepInvalidEvent;
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2025 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class RevalidatePreviousStepsFlow extends FormFlow implements EventSubscriberInterface {

	use LogEventCallsTrait;

	/**
	 * {@inheritDoc}
	 */
	public function setEventDispatcher(EventDispatcherInterface $dispatcher) {
		parent::setEventDispatcher($dispatcher);

		$dispatcher->removeSubscriber($this);
		$dispatcher->addSubscriber($this);
	}

	/**
	 * {@inheritDoc}
	 */
	public static function getSubscribedEvents() : array {
		return [
			FormFlowEvents::PREVIOUS_STEP_INVALID => 'onPreviousStepInvalid',
		];
	}

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		return [
			[
				'label' => 'step1',
			],
			[
				'label' => 'step2',
			],
			[
				'label' => 'step3',
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public function bind($formData) {
		$this->clearLoggedEventCalls();

		parent::bind($formData);
	}

	public function onPreviousStepInvalid(PreviousStepInvalidEvent $event) {
		if ($event->getFlow() !== $this) {
			return;
		}

		$this->logEventCall('onPreviousStepInvalid #' . $event->getInvalidStepNumber());
	}

}
