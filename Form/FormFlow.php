<?php

namespace Craue\FormFlowBundle\Form;

use Craue\FormFlowBundle\Event\PostBindRequest;
use Craue\FormFlowBundle\Event\PreBind;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @copyright 2011 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class FormFlow {

	const TRANSITION_BACK = 'back';
	const TRANSITION_RESET = 'reset';

	protected $formType;
	protected $formFactory;
	protected $request;
	protected $session;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

	protected $id;
	protected $formStepKey;
	protected $formTransitionKey;
	protected $sessionDataKey;
	protected $validationGroupPrefix;
	protected $maxSteps;
	protected $currentStep;
	protected $transition;
	protected $stepDescriptions = null;

	protected $skipSteps = array();

	protected $allowDynamicStepNavigation = false;
	protected $dynamicStepNavigationParameter = 'step';

	public function setFormFactory(FormFactoryInterface $formFactory) {
		$this->formFactory = $formFactory;
	}

	public function setRequest(Request $request) {
		$this->request = $request;
	}

	public function setSession(Session $session) {
		$this->session = $session;
	}

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

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
		if (empty($this->sessionDataKey)) {
			$this->sessionDataKey = $this->id. '_data';
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

	public function setSessionDataKey($sessionDataKey) {
		$this->sessionDataKey = $sessionDataKey;
	}

	public function getSessionDataKey() {
		return $this->sessionDataKey;
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
	 * @param int|array[int] $steps
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
	 * @param int|array[int] $steps
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
	 * @param int $step Assumed step to which skipped steps shall be applied to.
	 * @param int $direction Either 1 (to skip forwards) or -1 (to skip backwards).
	 * @return int Target step with skipping applied.
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
		$this->session->remove($this->sessionDataKey);
		$this->currentStep = $this->getFirstStep();
	}

	/**
	 * @return int First visible step, which may be greater than 1 if steps are skipped.
	 */
	public function getFirstStep() {
		return $this->applySkipping(1);
	}

	/**
	 * @return int Last visible step, which may be less than $this->maxSteps if steps are skipped.
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

		return array_key_exists($step, $this->getSessionData());
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
		if (!$this->allowDynamicStepNavigation && $this->request->getMethod() === 'GET') {
			$this->reset();
			return;
		}

		if ($this->getRequestedTransition() === self::TRANSITION_RESET) {
			$this->reset();
			return;
		}

        $event = new PreBind($formData);
        $this->dispatcher->dispatch(FormFlowEvents::PRE_BIND, $event);

		$requestedStep = $this->determineCurrentStep();

		// ensure that requested step fits the current progress
		if ($requestedStep > 1 && !$this->isStepDone($requestedStep - 1)) {
			$this->reset();
			return;
		}

		$this->currentStep = $requestedStep;
		$this->applyDataFromSavedSteps($formData);
		if (!$this->allowDynamicStepNavigation && $this->getRequestedTransition() === self::TRANSITION_BACK) {
			$this->invalidateStepData($this->currentStep);
		}
	}

	public function saveCurrentStepData() {
		$sessionData = $this->getSessionData();

		$sessionData[$this->currentStep] = array_replace_recursive(
			$this->request->request->get($this->formType->getName(), array()),
			$this->request->files->get($this->formType->getName(), array())
		);

		$this->setSessionData($sessionData);
	}

	/**
	 * Invalidates data for steps >= $fromStep.
	 * @param int $fromStep
	 */
	public function invalidateStepData($fromStep) {
		$sessionData = $this->getSessionData();

		for ($step = $fromStep; $step < $this->maxSteps; ++$step) {
			unset($sessionData[$step]);
		}

		$this->setSessionData($sessionData);
	}

	/**
	 * Updates form data class with form data from previously saved steps.
	 * @param mixed $formData
	 * @param array $formOptions
	 */
	public function applyDataFromSavedSteps($formData, array $formOptions = array()) {
		$sessionData = $this->getSessionData();

		/*
		 * Iteration $step === $this->currentStep is only needed to fill out the form when using the "back" button.
		 */
		for ($step = 1; $step <= $this->maxSteps; ++$step) {
			if ($this->isStepDone($step)) {
				$options = $this->getFormOptions($formData, $step, $formOptions);
				$stepForm = $this->formFactory->create($this->formType, $formData, $options);
				if (array_key_exists($step, $sessionData)) {
					$stepForm->bind($sessionData[$step]);
					$this->postBindSavedData($formData, $step); //flow.post_bind_saved_data
				}
			}
		}
	}

	public function createForm($formData, array $options = array()) {
		return $this->formFactory->create($this->formType, $formData,
				$this->getFormOptions($formData, $this->currentStep, $options));
	}

	public function getFormOptions($formData, $step, array $options = array()) {
		$options['flowStep'] = $step;
		$options['validation_groups'] = $this->validationGroupPrefix . $step;

		return $options;
	}

	public function getStepDescriptions() {
		if ($this->stepDescriptions === null) {
			$this->stepDescriptions = $this->loadStepDescriptions();
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
		if ($this->request->getMethod() === 'POST' && !in_array($this->getRequestedTransition(), array(
			self::TRANSITION_BACK,
			self::TRANSITION_RESET,
		))) {
			$form->bindRequest($this->request);
			$this->postBindRequest($form->getData()); //flow.post_bind_request

            $event = new PostBindRequest($form->getData(), $this->getCurrentStep());
            $this->dispatcher->dispatch(FormFlowEvents::POST_BIND_REQUEST, $event);

			if ($form->isValid()) {
				$this->postValidate($form->getData()); //flow.post_validate
				return true;
			}
		}

		return false;
	}

	/**
	 * Defines a description for each step used to render the step list.
	 * @return array Value with index 0 is description for step 1.
	 */
	protected function loadStepDescriptions() {
		return array();
	}

	/**
	 * Is called once prior to binding any (neither saved nor request) data.
	 * You can use this method to define steps to skip prior to determinating the current step, e.g. based on custom
	 * session data.
	 */
	protected function preBind() {
	}

	/**
	 * Is called for each step after binding its saved form data.
	 * @param mixed $formData
	 * @param int $step Step for which data has been bound.
	 */
	protected function postBindSavedData($formData, $step) {
	}

	/**
	 * Is called once for the current step after binding the request.
	 * @param mixed $formData
	 */
	protected function postBindRequest($formData) {
	}

	/**
	 * Is called once for the current step after validating the form data.
	 * @param mixed $formData
	 */
	protected function postValidate($formData) {
	}

	protected function getSessionData() {
		return $this->session->get($this->sessionDataKey, array());
	}

	protected function setSessionData(array $sessionData) {
		$this->session->set($this->sessionDataKey, $sessionData);
	}

}
