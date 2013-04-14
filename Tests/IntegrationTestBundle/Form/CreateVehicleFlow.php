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
class CreateVehicleFlow extends FormFlow implements EventSubscriberInterface {

	protected $maxSteps = 3;

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
			'wheels',
			'engine',
			'confirmation',
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
	public function reset() {
		parent::reset();
		$this->removeTempCanHaveEngine();
	}

	/**
	 * {@inheritDoc}
	 */
	public function createForm($formData, array $options = array()) {
		if ($this->currentStep === 1) {
			$this->removeSkipStep(2);
		}

		return parent::createForm($formData, $options);
	}

	protected function getTempCanHaveEngineSessionKey() {
		return $this->id . '_canHaveEngine';
	}

	protected function setTempCanHaveEngine() {
		$this->storage->set($this->getTempCanHaveEngineSessionKey(), true);
	}

	protected function canHaveEngine() {
		return $this->storage->get($this->getTempCanHaveEngineSessionKey(), false);
	}

	protected function removeTempCanHaveEngine() {
		$this->storage->remove($this->getTempCanHaveEngineSessionKey());
	}

	public function onPreBind(PreBindEvent $event) {
		if (!$this->canHaveEngine()) {
			$this->addSkipStep(2);
		}
	}

	public function onPostValidate(PostValidateEvent $event) {
		$formData = $event->getFormData();

		if ($this->currentStep >= 1) {
			if ($formData->canHaveEngine()) {
				$this->setTempCanHaveEngine();
				$this->removeSkipStep(2);
			} else {
				$this->removeTempCanHaveEngine();
				$this->addSkipStep(2);
			}
		}
	}

}
