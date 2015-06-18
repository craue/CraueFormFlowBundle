<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Event\GetStepsEvent;
use Craue\FormFlowBundle\Event\PostBindFlowEvent;
use Craue\FormFlowBundle\Event\PostBindRequestEvent;
use Craue\FormFlowBundle\Event\PostBindSavedDataEvent;
use Craue\FormFlowBundle\Event\PostValidateEvent;
use Craue\FormFlowBundle\Event\PreBindEvent;
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Demo1Flow extends FormFlow implements EventSubscriberInterface {

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'demo1';
	}

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
			FormFlowEvents::PRE_BIND => 'onPreBind',
			FormFlowEvents::GET_STEPS => 'onGetSteps',
			FormFlowEvents::POST_BIND_SAVED_DATA => 'onPostBindSavedData',
			FormFlowEvents::POST_BIND_FLOW => 'onPostBindFlow',
			FormFlowEvents::POST_BIND_REQUEST => 'onPostBindRequest',
			FormFlowEvents::POST_VALIDATE => 'onPostValidate',
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		return array(
			array(
				'label' => 'step1',
				'skip' => true,
			),
			array(
				'label' => 'step2',
			),
			array(
				'label' => 'step3',
			),
			array(
				'label' => 'step4',
			),
			array(
				'label' => 'step5',
				'skip' => true,
			),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function bind($formData) {
		$this->storage->set($this->getCalledEventsSessionKey(), array());
		parent::bind($formData);
	}

	public function getCalledEventsSessionKey() {
		return $this->getId() . '_debug_events_called';
	}

	protected function logEventCall($name) {
		$calledEvents = $this->storage->get($this->getCalledEventsSessionKey());
		$calledEvents[] = $name;
		$this->storage->set($this->getCalledEventsSessionKey(), $calledEvents);
	}

	public function onPreBind(PreBindEvent $event) {
		$this->logEventCall('onPreBind');
	}

	public function onGetSteps(GetStepsEvent $event) {
		$this->logEventCall('onGetSteps');
	}

	public function onPostBindSavedData(PostBindSavedDataEvent $event) {
		$this->logEventCall('onPostBindSavedData #' . $event->getStep());
	}

	public function onPostBindFlow(PostBindFlowEvent $event) {
		$this->logEventCall('onPostBindFlow #' . $event->getFlow()->getCurrentStepNumber());
	}

	public function onPostBindRequest(PostBindRequestEvent $event) {
		$this->logEventCall('onPostBindRequest');
	}

	public function onPostValidate(PostValidateEvent $event) {
		$this->logEventCall('onPostValidate');
	}

}
