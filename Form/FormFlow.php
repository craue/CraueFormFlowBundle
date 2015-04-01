<?php

namespace Craue\FormFlowBundle\Form;

use Craue\FormFlowBundle\Event\GetStepsEvent;
use Craue\FormFlowBundle\Event\PostBindFlowEvent;
use Craue\FormFlowBundle\Event\PostBindRequestEvent;
use Craue\FormFlowBundle\Event\PostBindSavedDataEvent;
use Craue\FormFlowBundle\Event\PostValidateEvent;
use Craue\FormFlowBundle\Event\PreBindEvent;
use Craue\FormFlowBundle\Event\PreviousStepInvalidEvent;
use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Craue\FormFlowBundle\Storage\DataManagerInterface;
use Craue\FormFlowBundle\Util\StringUtil;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @author Toni Uebernickel <tuebernickel@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class FormFlow implements FormFlowInterface {

	const TRANSITION_BACK = 'back';
	const TRANSITION_RESET = 'reset';

	/**
	 * @var FormFactoryInterface
	 */
	protected $formFactory;

	/**
	 * @var DataManagerInterface
	 */
	protected $dataManager;

	/**
	 * @var EventDispatcherInterface|null
	 */
	protected $eventDispatcher = null;

	/**
	 * @var string
	 */
	protected $transition;

	/**
	 * @var boolean
	 */
	protected $revalidatePreviousSteps = true;

	/**
	 * @var boolean
	 */
	protected $allowDynamicStepNavigation = false;

	/**
	 * @var boolean If file uploads should be handled by serializing them into the storage.
	 */
	protected $handleFileUploads = true;

	/**
	 * @var string|null Directory for storing temporary files while handling uploads. If <code>null</code>, the system's default will be used.
	 */
	protected $handleFileUploadsTempDir = null;

	/**
	 * @var boolean
	 */
	protected $allowRedirectAfterSubmit = false;

	/**
	 * @var string
	 */
	protected $dynamicStepNavigationInstanceParameter = 'instance';

	/**
	 * @var string
	 */
	protected $dynamicStepNavigationStepParameter = 'step';

	/**
	 * @var Request|null
	 */
	private $request = null;

	/**
	 * @var string|null Is only null if not yet initialized.
	 */
	private $id = null;

	/**
	 * @var string|null Is only null if not yet initialized.
	 */
	private $instanceKey = null;

	/**
	 * @var string|null Is only null if not yet initialized.
	 */
	private $instanceId = null;

	/**
	 * @var string|null Is only null if not yet initialized.
	 */
	private $formStepKey = null;

	/**
	 * @var string|null Is only null if not yet initialized.
	 */
	private $formTransitionKey = null;

	/**
	 * @var string|null Is only null if not yet initialized.
	 */
	private $validationGroupPrefix = null;

	/**
	 * @var StepInterface[]|null Is only null if not yet initialized.
	 */
	private $steps = null;

	/**
	 * @var integer|null Is only null if not yet initialized.
	 */
	private $stepCount = null;

	/**
	 * @var string[]|null Is only null if not yet initialized.
	 */
	private $stepLabels = null;

	/**
	 * @var mixed|null Is only null if not yet initialized.
	 */
	private $formData = null;

	/**
	 * @var integer|null Is only null if not yet initialized.
	 */
	private $currentStepNumber = null;

	/**
	 * @var FormInterface[]
	 */
	private $stepForms = array();

	/**
	 * Options applied to forms of all steps.
	 * @var array
	 */
	private $genericFormOptions = array();

	/**
	 * {@inheritDoc}
	 */
	public function setFormFactory(FormFactoryInterface $formFactory) {
		$this->formFactory = $formFactory;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setRequest(Request $request = null) {
		$this->request = $request;
	}

	/**
	 * @return Request
	 * @throws \RuntimeException If the request is not available.
	 */
	public function getRequest() {
		if ($this->request === null) {
			throw new \RuntimeException('The request is not available.');
		}

		return $this->request;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setDataManager(DataManagerInterface $dataManager) {
		$this->dataManager = $dataManager;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDataManager() {
		return $this->dataManager;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setEventDispatcher(EventDispatcherInterface $eventDispatcher) {
		$this->eventDispatcher = $eventDispatcher;
	}

	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getId() {
		if ($this->id === null) {
			$this->id = 'flow_' . $this->getName();
		}

		return $this->id;
	}

	public function setInstanceKey($instanceKey) {
		$this->instanceKey = $instanceKey;
	}

	public function getInstanceKey() {
		if ($this->instanceKey === null) {
			$this->instanceKey = $this->getId() . '_instance';
		}

		return $this->instanceKey;
	}

	public function setInstanceId($instanceId) {
		$this->instanceId = $instanceId;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getInstanceId() {
		if ($this->instanceId === null) {
			$this->instanceId = $this->getId();
		}

		return $this->instanceId;
	}

	public function setFormStepKey($formStepKey) {
		$this->formStepKey = $formStepKey;
	}

	public function getFormStepKey() {
		if ($this->formStepKey === null) {
			$this->formStepKey = $this->getId() . '_step';
		}

		return $this->formStepKey;
	}

	public function setFormTransitionKey($formTransitionKey) {
		$this->formTransitionKey = $formTransitionKey;
	}

	public function getFormTransitionKey() {
		if ($this->formTransitionKey === null) {
			$this->formTransitionKey = $this->getId() . '_transition';
		}

		return $this->formTransitionKey;
	}

	public function setValidationGroupPrefix($validationGroupPrefix) {
		$this->validationGroupPrefix = $validationGroupPrefix;
	}

	public function getValidationGroupPrefix() {
		if ($this->validationGroupPrefix === null) {
			$this->validationGroupPrefix = $this->getId() . '_step';
		}

		return $this->validationGroupPrefix;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStepCount() {
		if ($this->stepCount === null) {
			$this->stepCount = count($this->getSteps());
		}

		return $this->stepCount;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFormData() {
		if ($this->formData === null) {
			throw new \RuntimeException('Form data has not been evaluated yet and thus cannot be accessed.');
		}

		return $this->formData;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getCurrentStepNumber() {
		if ($this->currentStepNumber === null) {
			throw new \RuntimeException('The current step has not been determined yet and thus cannot be accessed.');
		}

		return $this->currentStepNumber;
	}

	public function setRevalidatePreviousSteps($revalidatePreviousSteps) {
		$this->revalidatePreviousSteps = (boolean) $revalidatePreviousSteps;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isRevalidatePreviousSteps() {
		return $this->revalidatePreviousSteps;
	}

	public function setAllowDynamicStepNavigation($allowDynamicStepNavigation) {
		$this->allowDynamicStepNavigation = (boolean) $allowDynamicStepNavigation;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAllowDynamicStepNavigation() {
		return $this->allowDynamicStepNavigation;
	}

	public function setHandleFileUploads($handleFileUploads) {
		$this->handleFileUploads = (boolean) $handleFileUploads;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isHandleFileUploads() {
		return $this->handleFileUploads;
	}

	public function setHandleFileUploadsTempDir($handleFileUploadsTempDir) {
		$this->handleFileUploadsTempDir = $handleFileUploadsTempDir !== null ? (string) $handleFileUploadsTempDir : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getHandleFileUploadsTempDir() {
		return $this->handleFileUploadsTempDir;
	}

	public function setAllowRedirectAfterSubmit($allowRedirectAfterSubmit) {
		$this->allowRedirectAfterSubmit = (boolean) $allowRedirectAfterSubmit;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAllowRedirectAfterSubmit() {
		return $this->allowRedirectAfterSubmit;
	}

	public function setDynamicStepNavigationInstanceParameter($dynamicStepNavigationInstanceParameter) {
		$this->dynamicStepNavigationInstanceParameter = $dynamicStepNavigationInstanceParameter;
	}

	public function getDynamicStepNavigationInstanceParameter() {
		return $this->dynamicStepNavigationInstanceParameter;
	}

	public function setDynamicStepNavigationStepParameter($dynamicStepNavigationStepParameter) {
		$this->dynamicStepNavigationStepParameter = $dynamicStepNavigationStepParameter;
	}

	public function getDynamicStepNavigationStepParameter() {
		return $this->dynamicStepNavigationStepParameter;
	}

	public function setGenericFormOptions(array $genericFormOptions) {
		$this->genericFormOptions = $genericFormOptions;
	}

	public function getGenericFormOptions() {
		return $this->genericFormOptions;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isStepSkipped($stepNumber) {
		return $this->getStep($stepNumber)->isSkipped();
	}

	/**
	 * @param integer $stepNumber Assumed step to which skipped steps shall be applied to.
	 * @param integer $direction Either 1 (to skip forwards) or -1 (to skip backwards).
	 * @return integer Target step number with skipping applied.
	 * @throws \InvalidArgumentException If the value of <code>$direction</code> is invalid.
	 */
	protected function applySkipping($stepNumber, $direction = 1) {
		if ($direction !== 1 && $direction !== -1) {
			throw new \InvalidArgumentException(sprintf('Argument of either -1 or 1 expected, "%s" given.',
					$direction));
		}

		while ($this->isStepSkipped($stepNumber)) {
			$stepNumber += $direction;
		}

		return $stepNumber;
	}

	/**
	 * {@inheritDoc}
	 */
	public function reset() {
		$this->dataManager->drop($this);
		$this->currentStepNumber = $this->getFirstStepNumber();

		// re-evaluate to not keep steps marked as skipped when resetting
		foreach ($this->getSteps() as $step) {
			$step->evaluateSkipping($this->currentStepNumber, $this);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFirstStepNumber() {
		return $this->applySkipping(1);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLastStepNumber() {
		return $this->applySkipping($this->getStepCount(), -1);
	}

	/**
	 * {@inheritDoc}
	 */
	public function nextStep() {
		$currentStepNumber = $this->currentStepNumber + 1;

		foreach ($this->getSteps() as $step) {
			$step->evaluateSkipping($currentStepNumber, $this);
		}

		// There is no "next" step as the target step exceeds the actual step count.
		if ($currentStepNumber > $this->getLastStepNumber()) {
			return false;
		}

		$currentStepNumber = $this->applySkipping($currentStepNumber);

		if ($currentStepNumber <= $this->getStepCount()) {
			$this->currentStepNumber = $currentStepNumber;

			return true;
		}

		return false; // should never be reached, but just in case
	}

	/**
	 * {@inheritDoc}
	 */
	public function isStepDone($stepNumber) {
		if ($this->isStepSkipped($stepNumber)) {
			return true;
		}

		return array_key_exists($stepNumber, $this->retrieveStepData());
	}

	public function getRequestedTransition() {
		if (empty($this->transition)) {
			$this->transition = strtolower($this->getRequest()->request->get($this->getFormTransitionKey()));
		}

		return $this->transition;
	}

	protected function getRequestedStepNumber() {
		$defaultStepNumber = 1;

		$request = $this->getRequest();

		switch ($request->getMethod()) {
			case 'PUT':
			case 'POST':
				return intval($request->request->get($this->getFormStepKey(), $defaultStepNumber));
			case 'GET':
				return $this->allowDynamicStepNavigation || $this->allowRedirectAfterSubmit ?
						intval($request->get($this->dynamicStepNavigationStepParameter, $defaultStepNumber)) :
						$defaultStepNumber;
		}

		return $defaultStepNumber;
	}

	/**
	 * Finds out which step is the current one.
	 * @return integer
	 */
	protected function determineCurrentStepNumber() {
		$requestedStepNumber = $this->getRequestedStepNumber();

		if ($this->getRequestedTransition() === self::TRANSITION_BACK) {
			--$requestedStepNumber;
		}

		// ensure that the step number is within the range of defined steps to avoid a possible OutOfBoundsException
		$requestedStepNumber = max(min($requestedStepNumber, $this->getStepCount()), 1);

		$requestedStepNumber = $this->refineCurrentStepNumber($requestedStepNumber);

		if ($this->getRequestedTransition() === self::TRANSITION_BACK) {
			$requestedStepNumber = $this->applySkipping($requestedStepNumber, -1);

			// re-evaluate to not keep following steps marked as skipped (after skipping them while going back)
			foreach ($this->getSteps() as $step) {
				$step->evaluateSkipping($requestedStepNumber, $this);
			}
		} else {
			$requestedStepNumber = $this->applySkipping($requestedStepNumber);
		}

		return $requestedStepNumber;
	}

	/**
	 * Refines the current step number by evaluating and considering skipped steps.
	 * @param integer $refinedStepNumber
	 * @return integer
	 */
	protected function refineCurrentStepNumber($refinedStepNumber) {
		foreach ($this->getSteps() as $step) {
			$stepSkippedOld = $step->isSkipped();

			$step->evaluateSkipping($refinedStepNumber, $this);

			if (!$stepSkippedOld && $step->isSkipped()) {
				return $this->refineCurrentStepNumber($refinedStepNumber);
			}
		}

		return $refinedStepNumber;
	}

	/**
	 * {@inheritDoc}
	 */
	public function bind($formData) {
		$this->setInstanceId($this->determineInstanceId());

		if ($this->hasListeners(FormFlowEvents::PRE_BIND)) {
			$event = new PreBindEvent($this);
			$this->eventDispatcher->dispatch(FormFlowEvents::PRE_BIND, $event);
		}

		$this->formData = $formData;

		$this->bindFlow();

		if ($this->hasListeners(FormFlowEvents::POST_BIND_FLOW)) {
			$event = new PostBindFlowEvent($this, $this->formData);
			$this->eventDispatcher->dispatch(FormFlowEvents::POST_BIND_FLOW, $event);
		}
	}

	protected function determineInstanceId() {
		$request = $this->getRequest();
		$instanceId = null;

		if ($this->allowDynamicStepNavigation || $this->allowRedirectAfterSubmit) {
			$instanceId = $request->get($this->getDynamicStepNavigationInstanceParameter());
		}

		if ($instanceId === null) {
			$instanceId = $request->request->get($this->getInstanceKey());
		}

		$instanceIdLength = 10;
		if ($instanceId === null || !StringUtil::isRandomString($instanceId, $instanceIdLength)) {
			$instanceId = StringUtil::generateRandomString($instanceIdLength);
		}

		return $instanceId;
	}

	protected function bindFlow() {
		$reset = false;

		if (!$this->allowDynamicStepNavigation && !$this->allowRedirectAfterSubmit && $this->getRequest()->isMethod('GET')) {
			$reset = true;
		}

		if ($this->getRequestedTransition() === self::TRANSITION_RESET) {
			$reset = true;
		}

		if (!$reset) {
			$this->applyDataFromSavedSteps();
		}

		$requestedStepNumber = $this->determineCurrentStepNumber();

		if ($reset) {
			$this->reset();
			return;
		}

		// ensure that the requested step fits the current progress
		if ($requestedStepNumber > $this->getFirstStepNumber()) {
			for ($step = $this->getFirstStepNumber(); $step < $requestedStepNumber; ++$step) {
				if (!$this->isStepDone($step)) {
					$this->reset();
					return;
				}
			}
		}

		$this->currentStepNumber = $requestedStepNumber;

		if (!$this->allowDynamicStepNavigation && $this->getRequestedTransition() === self::TRANSITION_BACK) {
			/*
			 * Don't invalidate data for the current step to properly show the filled out form for that step after
			 * pressing "back" and refreshing the page. Otherwise, the form would be blank since the data has already
			 * been invalidated previously.
			 */
			$this->invalidateStepData($this->currentStepNumber + 1);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function saveCurrentStepData(FormInterface $form) {
		$stepData = $this->retrieveStepData();

		$request = $this->getRequest();
		$formName = $form->getName();

		$currentStepData = $request->request->get($formName, array());

		if ($this->handleFileUploads) {
			$currentStepData = array_merge_recursive($currentStepData, $request->files->get($formName, array()));
		}

		$stepData[$this->currentStepNumber] = $currentStepData;

		$this->saveStepData($stepData);
	}

	/**
	 * Invalidates data for steps >= $fromStepNumber.
	 * @param integer $fromStepNumber
	 */
	public function invalidateStepData($fromStepNumber) {
		$stepData = $this->retrieveStepData();

		for ($step = $fromStepNumber, $stepCount = $this->getStepCount(); $step < $stepCount; ++$step) {
			unset($stepData[$step]);
		}

		$this->saveStepData($stepData);
	}

	/**
	 * Updates form data class with previously saved form data of all steps.
	 */
	protected function applyDataFromSavedSteps() {
		$stepData = $this->retrieveStepData();

		$this->stepForms = array();

		$options = array();
		if (!$this->revalidatePreviousSteps) {
			$options['validation_groups'] = array(); // disable validation
		}

		foreach ($this->getSteps() as $step) {
			$stepNumber = $step->getNumber();

			if (array_key_exists($stepNumber, $stepData)) {
				$stepForm = $this->createFormForStep($stepNumber, $options);
				$stepForm->submit($stepData[$stepNumber]); // the form is validated here

				if ($this->revalidatePreviousSteps) {
					$this->stepForms[$stepNumber] = $stepForm;
				}

				if ($this->hasListeners(FormFlowEvents::POST_BIND_SAVED_DATA)) {
					$event = new PostBindSavedDataEvent($this, $this->formData, $stepNumber);
					$this->eventDispatcher->dispatch(FormFlowEvents::POST_BIND_SAVED_DATA, $event);
				}
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function createForm() {
		return $this->createFormForStep($this->currentStepNumber);
	}

	public function getFormOptions($step, array $options = array()) {
		$options = array_merge($this->getGenericFormOptions(), $options);

		if (!array_key_exists('validation_groups', $options)) {
			$options['validation_groups'] = array(
				$this->getValidationGroupPrefix() . $step,
			);
		}

		$options['flow_instance'] = $this->getInstanceId();
		$options['flow_instance_key'] = $this->getInstanceKey();

		$options['flow_step'] = $step;
		$options['flow_step_key'] = $this->getFormStepKey();

		return $options;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStep($stepNumber) {
		if (!is_int($stepNumber)) {
			throw new InvalidTypeException($stepNumber, 'integer');
		}

		$steps = $this->getSteps();
		$index = $stepNumber - 1;

		if (array_key_exists($index, $steps)) {
			return $steps[$index];
		}

		throw new \OutOfBoundsException(sprintf('The step "%d" does not exist.', $stepNumber));
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSteps() {
		// The steps have been loaded already.
		if ($this->steps !== null) {
			return $this->steps;
		}

		if ($this->hasListeners(FormFlowEvents::GET_STEPS)) {
			$event = new GetStepsEvent($this);
			$this->eventDispatcher->dispatch(FormFlowEvents::GET_STEPS, $event);

			// A listener has provided the steps for this flow.
			if ($event->isPropagationStopped()) {
				$this->steps = $event->getSteps();

				return $this->steps;
			}
		}

		// There are either no listeners on the event at all or none created the steps for this flow, so load from configuration.
		$this->steps = $this->createStepsFromConfig($this->loadStepsConfig());

		return $this->steps;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStepLabels() {
		if ($this->stepLabels === null) {
			$stepLabels = array();

			foreach ($this->getSteps() as $step) {
				$stepLabels[] = $step->getLabel();
			}

			$this->stepLabels = $stepLabels;
		}

		return $this->stepLabels;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getCurrentStepLabel() {
		return $this->getStep($this->currentStepNumber)->getLabel();
	}

	/**
	 * {@inheritDoc}
	 */
	public function isValid(FormInterface $form) {
		$request = $this->getRequest();

		if (in_array($request->getMethod(), array('POST', 'PUT')) && !in_array($this->getRequestedTransition(), array(
			self::TRANSITION_BACK,
			self::TRANSITION_RESET,
		))) {
			$form->handleRequest($request);

			if ($this->hasListeners(FormFlowEvents::POST_BIND_REQUEST)) {
				$event = new PostBindRequestEvent($this, $form->getData(), $this->currentStepNumber);
				$this->eventDispatcher->dispatch(FormFlowEvents::POST_BIND_REQUEST, $event);
			}

			if ($this->revalidatePreviousSteps) {
				// check if forms of previous steps are still valid
				foreach ($this->stepForms as $stepNumber => $stepForm) {
					// ignore form of the current step
					if ($this->currentStepNumber === $stepNumber) {
						break;
					}

					// ignore forms of skipped steps
					if ($this->isStepSkipped($stepNumber)) {
						break;
					}

					if (!$stepForm->isValid()) {
						if ($this->hasListeners(FormFlowEvents::PREVIOUS_STEP_INVALID)) {
							$event = new PreviousStepInvalidEvent($this, $form, $stepNumber);
							$this->eventDispatcher->dispatch(FormFlowEvents::PREVIOUS_STEP_INVALID, $event);
						}

						return false;
					}
				}
			}

			if ($form->isValid()) {
				if ($this->hasListeners(FormFlowEvents::POST_VALIDATE)) {
					$event = new PostValidateEvent($this, $form->getData());
					$this->eventDispatcher->dispatch(FormFlowEvents::POST_VALIDATE, $event);
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * @param FormInterface $submittedForm
	 * @return boolean If a redirection should be performed.
	 */
	public function redirectAfterSubmit(FormInterface $submittedForm) {
		if ($this->allowRedirectAfterSubmit && in_array($this->getRequest()->getMethod(), array('POST', 'PUT'))) {
			switch ($this->getRequestedTransition()) {
				case self::TRANSITION_BACK:
				case self::TRANSITION_RESET:
					return true;
				default:
					// redirect after submit only if there are no errors for the submitted form
					return $submittedForm->isValid();
			}
		}

		return false;
	}

	/**
	 * Creates the form for the given step number.
	 * @param integer $stepNumber
	 * @param array $options
	 * @return FormInterface
	 */
	protected function createFormForStep($stepNumber, array $options = array()) {
		$formType = $this->getStep($stepNumber)->getType();
		$options = $this->getFormOptions($stepNumber, $options);

		return $this->formFactory->create($formType !== null ? $formType : 'form', $this->formData, $options);
	}

	/**
	 * Creates all steps from the given configuration.
	 * @param array $stepsConfig
	 * @return StepInterface[] Value with index 0 is step 1.
	 */
	public function createStepsFromConfig(array $stepsConfig) {
		$steps = array();

		// fix array indexes not starting at 0
		$stepsConfig = array_values($stepsConfig);

		foreach ($stepsConfig as $index => $stepConfig) {
			$steps[] = Step::createFromConfig($index + 1, $stepConfig);
		}

		return $steps;
	}

	/**
	 * Defines the configuration for all steps of this flow.
	 * @return array
	 */
	protected function loadStepsConfig() {
		return array();
	}

	protected function retrieveStepData() {
		return $this->dataManager->load($this);
	}

	protected function saveStepData(array $data) {
		$this->dataManager->save($this, $data);
	}

	/**
	 * @param string $eventName
	 * @return boolean
	 */
	protected function hasListeners($eventName) {
		return $this->eventDispatcher !== null && $this->eventDispatcher->hasListeners($eventName);
	}

	// methods for BC with third-party templates (e.g. MopaBootstrapBundle)

	public function getCurrentStep() {
		$this->triggerDeprecationError('Method ' . __METHOD__ . ' is deprecated since version 2.0. Use method getCurrentStepNumber instead.');
		return $this->getCurrentStepNumber();
	}

	public function getCurrentStepDescription() {
		$this->triggerDeprecationError('Method ' . __METHOD__ . ' is deprecated since version 2.0. Use method getCurrentStepLabel instead.');
		return $this->getCurrentStepLabel();
	}

	public function getMaxSteps() {
		$this->triggerDeprecationError('Method ' . __METHOD__ . ' is deprecated since version 2.0. Use method getStepCount instead.');
		return $this->getStepCount();
	}

	public function getStepDescriptions() {
		$this->triggerDeprecationError('Method ' . __METHOD__ . ' is deprecated since version 2.0. Use method getStepLabels instead.');
		return $this->getStepLabels();
	}

	public function getFirstStep() {
		$this->triggerDeprecationError('Method ' . __METHOD__ . ' is deprecated since version 2.0. Use method getFirstStepNumber instead.');
		return $this->getFirstStepNumber();
	}

	public function getLastStep() {
		$this->triggerDeprecationError('Method ' . __METHOD__ . ' is deprecated since version 2.0. Use method getLastStepNumber instead.');
		return $this->getLastStepNumber();
	}

	public function hasSkipStep($stepNumber) {
		$this->triggerDeprecationError('Method ' . __METHOD__ . ' is deprecated since version 2.0. Use method isStepSkipped instead.');
		return $this->isStepSkipped($stepNumber);
	}

	protected function triggerDeprecationError($message) {
		trigger_error($message, E_USER_DEPRECATED);
	}

}
