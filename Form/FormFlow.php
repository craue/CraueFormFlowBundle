<?php

namespace Craue\FormFlowBundle\Form;

use Craue\FormFlowBundle\Event\PostBindRequestEvent;
use Craue\FormFlowBundle\Event\PostBindSavedDataEvent;
use Craue\FormFlowBundle\Event\PostValidateEvent;
use Craue\FormFlowBundle\Event\PreBindEvent;
use Craue\FormFlowBundle\Storage\StorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @author Toni Uebernickel <tuebernickel@gmail.com>
 * @copyright 2011-2012 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class FormFlow {

	const TRANSITION_BACK = 'back';
	const TRANSITION_RESET = 'reset';

	/**
	 * @var FormTypeInterface
	 */
	protected $formType;

	/**
	 * @var FormFactoryInterface
	 */
	protected $formFactory;

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var StorageInterface
	 */
	protected $storage;

	/**
	 * @var EventDispatcherInterface
	 */
	protected $eventDispatcher;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $formStepKey;

	/**
	 * @var string
	 */
	protected $formTransitionKey;

	/**
	 * @var string
	 */
	protected $stepDataKey;

	/**
	 * @var string
	 */
	protected $validationGroupPrefix;

	/**
	 * @var integer
	 */
	protected $maxSteps;

	/**
	 * @var integer
	 */
	protected $currentStep;

	/**
	 * @var string
	 */
	protected $transition;

	/**
	 * @var null|string[] Is only null if not initialized.
	 */
	protected $stepDescriptions = null;

	/**
	 * @var integer[]
	 */
	protected $skipSteps = array();

	/**
	 * @var boolean
	 */
	protected $allowDynamicStepNavigation = false;

	/**
	 * @var string
	 */
	protected $dynamicStepNavigationParameter = 'step';

	/**
	 * @param FormFactoryInterface $formFactory
	 */
	public function setFormFactory(FormFactoryInterface $formFactory) {
		$this->formFactory = $formFactory;
	}

	/**
	 * @param Request $request
	 */
	public function setRequest(Request $request) {
		$this->request = $request;
	}

	/**
	 * @param StorageInterface $storage
	 */
	public function setStorage(StorageInterface $storage) {
		$this->storage = $storage;
	}

	/**
	 * @param EventDispatcherInterface $eventDispatcher
	 */
	public function setEventDispatcher(EventDispatcherInterface $eventDispatcher) {
		$this->eventDispatcher = $eventDispatcher;
	}

	/**
	 * @param FormTypeInterface $formType
	 */
	public function setFormType(FormTypeInterface $formType) {
		$this->formType = $formType;
		if (empty($this->id)) {
			$this->id = 'flow_' . $this->formType->getName();
		}
		if (empty($this->validationGroupPrefix)) {
			$this->validationGroupPrefix = $this->id. '_step';
		}
		if (empty($this->formStepKey)) {
			$this->formStepKey = $this->id. '_step';
		}
		if (empty($this->formTransitionKey)) {
			$this->formTransitionKey = $this->id. '_transition';
		}
		if (empty($this->stepDataKey)) {
			$this->stepDataKey = $this->id. '_data';
		}
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function setFormStepKey($formStepKey) {
		$this->formStepKey = $formStepKey;
	}

	public function getFormStepKey() {
		return $this->formStepKey;
	}

	public function setFormTransitionKey($formTransitionKey) {
		$this->formTransitionKey = $formTransitionKey;
	}

	public function getFormTransitionKey() {
		return $this->formTransitionKey;
	}

	public function setStepDataKey($stepDataKey) {
		$this->stepDataKey = $stepDataKey;
	}

	public function getStepDataKey() {
		return $this->stepDataKey;
	}

	public function setValidationGroupPrefix($validationGroupPrefix) {
		$this->validationGroupPrefix = $validationGroupPrefix;
	}

	public function getValidationGroupPrefix() {
		return $this->validationGroupPrefix;
	}

	public function getFormType() {
		return $this->formType;
	}

	public function setMaxSteps($maxSteps) {
		$this->maxSteps = $maxSteps;
	}

	public function getMaxSteps() {
		return $this->maxSteps;
	}

	public function setCurrentStep($currentStep) {
		$this->currentStep = $currentStep;
	}

	public function getCurrentStep() {
		return $this->currentStep;
	}

	public function setAllowDynamicStepNavigation($allowDynamicStepNavigation) {
		$this->allowDynamicStepNavigation = $allowDynamicStepNavigation;
	}

	public function isAllowDynamicStepNavigation() {
		return $this->allowDynamicStepNavigation;
	}

	public function setDynamicStepNavigationParameter($dynamicStepNavigationParameter) {
		$this->dynamicStepNavigationParameter = $dynamicStepNavigationParameter;
	}

	public function getDynamicStepNavigationParameter() {
		return $this->dynamicStepNavigationParameter;
	}

	/**
	 * @param integer|integer[] $steps
	 */
	public function addSkipStep($steps) {
		if (is_scalar($steps)) {
			$steps = array($steps);
		}

		foreach ($steps as $step) {
			if (!in_array($step, $this->skipSteps)) {
				$this->skipSteps[] = $step;
			}
		}
	}

	/**
	 * @param integer|integer[] $steps
	 */
	public function removeSkipStep($steps) {
		if (is_scalar($steps)) {
			$steps = array($steps);
		}

		foreach ($steps as $step) {
			$key = array_search($step, $this->skipSteps, true);
			if ($key !== false) {
				unset($this->skipSteps[$key]);
				$this->skipSteps = array_values($this->skipSteps);
			}
		}
	}

	public function hasSkipStep($step) {
		return in_array($step, $this->skipSteps);
	}

	/**
	 * @param integer $step Assumed step to which skipped steps shall be applied to.
	 * @param integer $direction Either 1 (to skip forwards) or -1 (to skip backwards).
	 * @return integer Target step with skipping applied.
	 */
	public function applySkipping($step, $direction = 1) {
		if ($direction !== 1 && $direction !== -1) {
			throw new \InvalidArgumentException(sprintf('Argument of either -1 or 1 expected, "%s" given.',
					$direction));
		}

		while ($this->hasSkipStep($step)) {
			$step += $direction;
		}

		return $step;
	}

	public function reset() {
		$this->storage->remove($this->stepDataKey);
		$this->currentStep = $this->getFirstStep();
	}

	/**
	 * @return integer First visible step, which may be greater than 1 if steps are skipped.
	 */
	public function getFirstStep() {
		return $this->applySkipping(1);
	}

	/**
	 * @return integer Last visible step, which may be less than $this->maxSteps if steps are skipped.
	 */
	public function getLastStep() {
		return $this->applySkipping($this->maxSteps, -1);
	}

	public function nextStep() {
		$this->currentStep = $this->applySkipping(++$this->currentStep);

		return $this->currentStep <= $this->maxSteps;
	}

	public function isStepDone($step) {
		if ($this->hasSkipStep($step)) {
			return true;
		}

		return array_key_exists($step, $this->retrieveStepData());
	}

	public function getRequestedTransition() {
		if (empty($this->transition)) {
			$this->transition = strtolower($this->request->request->get($this->formTransitionKey));
		}

		return $this->transition;
	}

	public function getRequestedStep() {
		$defaultStep = 1;

		switch ($this->request->getMethod()) {
			case 'POST':
				return intval($this->request->request->get($this->formStepKey, $defaultStep));
			case 'GET':
				return $this->allowDynamicStepNavigation ?
						intval($this->request->query->get($this->dynamicStepNavigationParameter, $defaultStep)) :
						$defaultStep;
		}

		return $defaultStep;
	}

	public function determineCurrentStep() {
		$requestedStep = $this->getRequestedStep();

		if ($this->getRequestedTransition() === self::TRANSITION_BACK) {
			$requestedStep = $this->applySkipping(--$requestedStep, -1);
		}

		// skip steps
		$requestedStep = $this->applySkipping($requestedStep);

		// ensure: first step <= $requestedStep <= $this->maxSteps
		$requestedStep = min(max($this->getFirstStep(), $requestedStep), $this->maxSteps);

		return $requestedStep;
	}

	public function bind($formData) {
		if (!$this->allowDynamicStepNavigation && $this->request->isMethod('GET')) {
			$this->reset();
			return;
		}

		if ($this->getRequestedTransition() === self::TRANSITION_RESET) {
			$this->reset();
			return;
		}

		$event = new PreBindEvent($this);
		$this->eventDispatcher->dispatch(FormFlowEvents::PRE_BIND, $event);

		$requestedStep = $this->determineCurrentStep();

		// ensure that requested step fits the current progress
		if ($requestedStep > 1 && !$this->isStepDone($requestedStep - 1)) {
			$this->reset();
			return;
		}

		$this->currentStep = $requestedStep;
		$this->applyDataFromSavedSteps($formData);
		if (!$this->allowDynamicStepNavigation && $this->getRequestedTransition() === self::TRANSITION_BACK) {
			/*
			 * Don't invalidate data for the current step to properly show the filled out form for that step after
			 * pressing "back" and refreshing the page. Otherwise, the form would be blank since the data has already
			 * been invalidated previously.
			 */
			$this->invalidateStepData($this->currentStep + 1);
		}
	}

	public function saveCurrentStepData() {
		$stepData = $this->retrieveStepData();

		$stepData[$this->currentStep] = $this->request->request->get($this->formType->getName(), array());

		$this->saveStepData($stepData);
	}

	/**
	 * Invalidates data for steps >= $fromStep.
	 * @param integer $fromStep
	 */
	public function invalidateStepData($fromStep) {
		$stepData = $this->retrieveStepData();

		for ($step = $fromStep; $step < $this->maxSteps; ++$step) {
			unset($stepData[$step]);
		}

		$this->saveStepData($stepData);
	}

	/**
	 * Updates form data class with form data from previously saved steps.
	 * @param mixed $formData
	 * @param array $options
	 */
	public function applyDataFromSavedSteps($formData, array $options = array()) {
		$stepData = $this->retrieveStepData();

		/*
		 * Iteration $step === $this->currentStep is only needed to fill out the form when using the "back" button.
		 */
		for ($step = 1; $step <= $this->maxSteps; ++$step) {
			if ($this->isStepDone($step)) {
				if (array_key_exists($step, $stepData)) {
					$stepForm = $this->createFormForStep($formData, $step, $options);
					$stepForm->bind($stepData[$step]);

					$event = new PostBindSavedDataEvent($this, $formData, $step);
					$this->eventDispatcher->dispatch(FormFlowEvents::POST_BIND_SAVED_DATA, $event);
				}
			}
		}
	}

	/**
	 * Creates the form for the current step.
	 * @param mixed $formData
	 * @param array $options
	 * @return FormInterface
	 */
	public function createForm($formData, array $options = array()) {
		return $this->createFormForStep($formData, $this->currentStep, $options);
	}

	public function getFormOptions($formData, $step, array $options = array()) {
		$options['flowStep'] = $step;

		if (!array_key_exists('validation_groups', $options)) {
			$options['validation_groups'] = $this->validationGroupPrefix . $step;
		}

		return $options;
	}

	public function getStepDescriptions() {
		if ($this->stepDescriptions === null) {
			$this->stepDescriptions = $this->loadStepDescriptions();
		}

		$stepDescriptionsCount = count($this->stepDescriptions);
		if ($stepDescriptionsCount > 0 && $stepDescriptionsCount !== $this->maxSteps) {
			throw new \RuntimeException(sprintf('The number of steps (%u) doesn\'t match the number of step descriptions (%u). Either update the descriptions or remove them.',
					$this->maxSteps, $stepDescriptionsCount
			));
		}

		return $this->stepDescriptions;
	}

	public function getCurrentStepDescription() {
		$stepDescriptions = $this->getStepDescriptions();
		$index = $this->currentStep - 1;

		if (array_key_exists($index, $stepDescriptions)) {
			return $stepDescriptions[$index];
		}

		return null;
	}

	public function isValid(FormInterface $form) {
		if ($this->request->isMethod('POST') && !in_array($this->getRequestedTransition(), array(
			self::TRANSITION_BACK,
			self::TRANSITION_RESET,
		))) {
			$form->bindRequest($this->request);

			$event = new PostBindRequestEvent($this, $form->getData(), $this->currentStep);
			$this->eventDispatcher->dispatch(FormFlowEvents::POST_BIND_REQUEST, $event);

			if ($form->isValid()) {
				$event = new PostValidateEvent($this, $form->getData());
				$this->eventDispatcher->dispatch(FormFlowEvents::POST_VALIDATE, $event);

				return true;
			}
		}

		return false;
	}

	/**
	 * Creates the form for the given step.
	 * @param mixed $formData
	 * @param integer $step
	 * @param array $options
	 * @return FormInterface
	 */
	protected function createFormForStep($formData, $step, array $options = array()) {
		if (!$this->formType instanceof FormTypeInterface) {
			throw new \RuntimeException(sprintf('The form type has to be an instance of type "%s", but "%s" given.',
					'Symfony\Component\Form\FormTypeInterface',
					is_object($this->formType) ? get_class($this->formType) : gettype($this->formType)
			));
		}

		$options = $this->getFormOptions($formData, $step, $options);

		return $this->formFactory->create($this->formType, $formData, $options);
	}

	/**
	 * Defines a description for each step used to render the step list.
	 * @return string[] Value with index 0 is description for step 1.
	 */
	protected function loadStepDescriptions() {
		return array();
	}

	protected function retrieveStepData() {
		return $this->storage->get($this->stepDataKey, array());
	}

	protected function saveStepData(array $data) {
		$this->storage->set($this->stepDataKey, $data);
	}

}
