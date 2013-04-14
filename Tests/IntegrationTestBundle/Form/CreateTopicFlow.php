<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

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
class CreateTopicFlow extends FormFlow implements EventSubscriberInterface {

	protected $allowDynamicStepNavigation = true;
	protected $maxSteps = 4;

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
			FormFlowEvents::POST_VALIDATE => 'onPostValidate',
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepDescriptions() {
		return array(
			'basics',
			'comment',
			'bug_details',
			'confirmation',
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFormOptions($formData, $step, array $options = array()) {
		$options = parent::getFormOptions($formData, $step, $options);

		$options['cascade_validation'] = true;

		if ($step > 1) {
			$options['isBugReport'] = $formData->isBugReport();
		}

		return $options;
	}

	/**
	 * {@inheritDoc}
	 */
	public function reset() {
		parent::reset();
		$this->removeTempIsBugReport();
	}

	/**
	 * {@inheritDoc}
	 */
	public function createForm($formData, array $options = array()) {
		if ($this->currentStep === 1) {
			$this->removeSkipStep(3);
		}

		return parent::createForm($formData, $options);
	}

	protected function getTempIsBugReportSessionKey() {
		return $this->id . '_isBugReport';
	}

	protected function setTempIsBugReport() {
		$this->storage->set($this->getTempIsBugReportSessionKey(), true);
	}

	protected function isBugReport() {
		return $this->storage->get($this->getTempIsBugReportSessionKey(), false);
	}

	protected function removeTempIsBugReport() {
		$this->storage->remove($this->getTempIsBugReportSessionKey());
	}

	public function onPreBind(PreBindEvent $event) {
		if (!$this->isBugReport()) {
			$this->addSkipStep(3);
		}
	}

	public function onPostValidate(PostValidateEvent $event) {
		$formData = $event->getFormData();

		if ($this->currentStep >= 1) {
			if ($formData->isBugReport()) {
				$this->setTempIsBugReport();
				$this->removeSkipStep(3);
			} else {
				$this->removeTempIsBugReport();
				$this->addSkipStep(3);
			}
		}
	}

}
