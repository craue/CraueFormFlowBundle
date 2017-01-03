<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Event\PreviousStepInvalidEvent;
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class RevalidatePreviousStepsFlow extends FormFlow implements EventSubscriberInterface {

	/**
	 * {@inheritDoc}
	 */
	public function setEventDispatcher(EventDispatcherInterface $dispatcher) {
		parent::setEventDispatcher($dispatcher);
		$dispatcher->addSubscriber($this);
	}

	/**
	 * {@inheritDoc}
	 */
	public static function getSubscribedEvents() {
		return array(
			FormFlowEvents::PREVIOUS_STEP_INVALID => 'onPreviousStepInvalid',
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		return array(
			array(
				'label' => 'step1',
			),
			array(
				'label' => 'step2',
			),
			array(
				'label' => 'step3',
			),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function bind($formData) {
		$this->dataManager->getStorage()->set($this->getCalledEventsSessionKey(), array());
		parent::bind($formData);
	}

	public function getCalledEventsSessionKey() {
		return $this->getId() . '_debug_events_called';
	}

	protected function logEventCall($name) {
		$calledEvents = $this->dataManager->getStorage()->get($this->getCalledEventsSessionKey());
		$calledEvents[] = $name;
		$this->dataManager->getStorage()->set($this->getCalledEventsSessionKey(), $calledEvents);
	}

	public function onPreviousStepInvalid(PreviousStepInvalidEvent $event) {
		$this->logEventCall('onPreviousStepInvalid #' . $event->getInvalidStepNumber());
	}

}
