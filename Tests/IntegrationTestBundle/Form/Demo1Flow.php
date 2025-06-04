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
 * @copyright 2011-2025 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Demo1Flow extends FormFlow implements EventSubscriberInterface {

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
			FormFlowEvents::PRE_BIND => 'onPreBind',
			FormFlowEvents::GET_STEPS => 'onGetSteps',
			FormFlowEvents::POST_BIND_SAVED_DATA => 'onPostBindSavedData',
			FormFlowEvents::POST_BIND_FLOW => 'onPostBindFlow',
			FormFlowEvents::POST_BIND_REQUEST => 'onPostBindRequest',
			FormFlowEvents::POST_VALIDATE => 'onPostValidate',
		];
	}

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		return [
			[
				'label' => 'step1',
				'skip' => true,
			],
			[
				'label' => 'step2',
			],
			[
				'label' => 'step3',
			],
			[
				'label' => 'step4',
			],
			[
				'label' => 'step5',
				'skip' => true,
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

	public function onPreBind(PreBindEvent $event) {
		if ($event->getFlow() !== $this) {
			return;
		}

		$this->logEventCall('onPreBind');
	}

	public function onGetSteps(GetStepsEvent $event) {
		if ($event->getFlow() !== $this) {
			return;
		}

		$this->logEventCall('onGetSteps');
	}

	public function onPostBindSavedData(PostBindSavedDataEvent $event) {
		if ($event->getFlow() !== $this) {
			return;
		}

		$this->logEventCall('onPostBindSavedData #' . $event->getStepNumber());
	}

	public function onPostBindFlow(PostBindFlowEvent $event) {
		if ($event->getFlow() !== $this) {
			return;
		}

		$this->logEventCall('onPostBindFlow #' . $event->getFlow()->getCurrentStepNumber());
	}

	public function onPostBindRequest(PostBindRequestEvent $event) {
		if ($event->getFlow() !== $this) {
			return;
		}

		$this->logEventCall('onPostBindRequest');
	}

	public function onPostValidate(PostValidateEvent $event) {
		if ($event->getFlow() !== $this) {
			return;
		}

		$this->logEventCall('onPostValidate');
	}

}
