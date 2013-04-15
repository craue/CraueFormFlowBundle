<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

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
 * @copyright 2011-2013 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Demo1Flow extends FormFlow implements EventSubscriberInterface {

	protected $maxSteps = 5;

	protected $skipSteps = array(1, 5);

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
			FormFlowEvents::POST_BIND_SAVED_DATA => 'onPostBindSavedData',
			FormFlowEvents::POST_BIND_REQUEST => 'onPostBindRequest',
			FormFlowEvents::POST_VALIDATE => 'onPostValidate',
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepDescriptions() {
		return array(
			'step1',
			'step2',
			'step3',
			'step4',
			'step5',
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFormOptions($formData, $step, array $options = array()) {
		$options = parent::getFormOptions($formData, $step, $options);

		$options['cascade_validation'] = true;

		return $options;
	}

	/**
	 * {@inheritDoc}
	 */
	public function bind($formData) {
		$this->storage->set($this->getCalledEventsSessionKey(), array());
		parent::bind($formData);
	}

	public function getCalledEventsSessionKey() {
		return $this->id . '_debug_events_called';
	}

	protected function logEventCall($name) {
		$calledEvents = $this->storage->get($this->getCalledEventsSessionKey());
		$calledEvents[] = $name;
		$this->storage->set($this->getCalledEventsSessionKey(), $calledEvents);
	}

	public function onPreBind(PreBindEvent $event) {
		$this->logEventCall('onPreBind');
	}

	public function onPostBindSavedData(PostBindSavedDataEvent $event) {
		$this->logEventCall('onPostBindSavedData #' . $event->getStep());
	}

	public function onPostBindRequest(PostBindRequestEvent $event) {
		$this->logEventCall('onPostBindRequest');
	}

	public function onPostValidate(PostValidateEvent $event) {
		$this->logEventCall('onPostValidate');
	}

}
