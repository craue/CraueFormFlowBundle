<?php

namespace Craue\FormFlowBundle\Form;

use Craue\FormFlowBundle\Event\FlowExpiredEvent;
use Craue\FormFlowBundle\Event\FormFlowEvent;
use Craue\FormFlowBundle\Event\GetStepsEvent;
use Craue\FormFlowBundle\Event\PostBindFlowEvent;
use Craue\FormFlowBundle\Event\PostBindRequestEvent;
use Craue\FormFlowBundle\Event\PostBindSavedDataEvent;
use Craue\FormFlowBundle\Event\PostValidateEvent;
use Craue\FormFlowBundle\Event\PreBindEvent;
use Craue\FormFlowBundle\Event\PreviousStepInvalidEvent;
use Craue\FormFlowBundle\Exception\AllStepsSkippedException;
use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Craue\FormFlowBundle\Storage\DataManagerInterface;
use Craue\FormFlowBundle\Util\StringUtil;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Validator\Constraints\GroupSequence;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @author Toni Uebernickel <tuebernickel@gmail.com>
 * @copyright 2011-2021 Christian Raue
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
	 * @var bool
	 */
	protected $revalidatePreviousSteps = true;

	/**
	 * @var bool
	 */
	protected $allowDynamicStepNavigation = false;

	/**
	 * @var bool If file uploads should be handled by serializing them into the storage.
	 */
	protected $handleFileUploads = true;

	/**
	 * @var string|null Directory for storing temporary files while handling uploads. If <code>null</code>, the system's default will be used.
	 */
	protected $handleFileUploadsTempDir = null;

	/**
	 * @var bool
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
	 * @var RequestStack
	 */
	private $requestStack;

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
	 * @var int|null Is only null if not yet initialized.
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
	 * @var int|null Is only null if not yet initialized.
	 */
	private $currentStepNumber = null;

	/**
	 * @var FormInterface[]
	 */
	private $stepForms = [];

	/**
	 * Options applied to forms of all steps.
	 * @var array
	 */
	private $genericFormOptions = [];

	/**
	 * Flow was determined to be expired.
	 * @var bool
	 */
	private $expired = false;

	/**
	 * {@inheritDoc}
	 */
	public function setFormFactory(FormFactoryInterface $formFactory) {
		$this->formFactory = $formFactory;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setRequestStack(RequestStack $requestStack) {
		$this->requestStack = $requestStack;
	}

	/**
	 * @return Request
	 * @throws \RuntimeException If the request is not available.
	 */
	public function getRequest() {
		$currentRequest = $this->requestStack->getCurrentRequest();

		if ($currentRequest === null) {
			throw new \RuntimeException('The request is not available.');
		}

		return $currentRequest;
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

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return StringUtil::fqcnToFlowName(get_class($this));
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
		$this->revalidatePreviousSteps = (bool) $revalidatePreviousSteps;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isRevalidatePreviousSteps() {
		return $this->revalidatePreviousSteps;
	}

	public function setAllowDynamicStepNavigation($allowDynamicStepNavigation) {
		$this->allowDynamicStepNavigation = (bool) $allowDynamicStepNavigation;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAllowDynamicStepNavigation() {
		return $this->allowDynamicStepNavigation;
	}

	public function setHandleFileUploads($handleFileUploads) {
		$this->handleFileUploads = (bool) $handleFileUploads;
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
		$this->allowRedirectAfterSubmit = (bool) $allowRedirectAfterSubmit;
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
	 * @param int $stepNumber Assumed step to which skipped steps shall be applied to.
	 * @param int $direction Either 1 (to skip forwards) or -1 (to skip backwards).
	 * @param int $boundsReached Internal counter to avoid endlessly bouncing back and forth.
	 * @return int Target step number with skipping applied.
	 * @throws \InvalidArgumentException If the value of <code>$direction</code> is invalid.
	 */
	protected function applySkipping($stepNumber, $direction = 1, $boundsReached = 0) {
		if ($direction !== 1 && $direction !== -1) {
			throw new \InvalidArgumentException(sprintf('Argument of either -1 or 1 expected, "%s" given.', $direction));
		}

		$stepNumber = $this->ensureStepNumberRange($stepNumber);

		if ($this->isStepSkipped($stepNumber)) {
			$stepNumber += $direction;

			// change direction if outer bounds are reached
			if ($direction === 1 && $stepNumber > $this->getStepCount()) {
				$direction = -1;
				++$boundsReached;
			} elseif ($direction === -1 && $stepNumber < 1) {
				$direction = 1;
				++$boundsReached;
			}

			if ($boundsReached > 2) {
				throw new AllStepsSkippedException();
			}

			return $this->applySkipping($stepNumber, $direction, $boundsReached);
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
	 * @return int
	 */
	protected function determineCurrentStepNumber() {
		$requestedStepNumber = $this->getRequestedStepNumber();

		if ($this->getRequestedTransition() === self::TRANSITION_BACK) {
			--$requestedStepNumber;
		}

		$requestedStepNumber = $this->ensureStepNumberRange($requestedStepNumber);
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
	 * Ensures that the step number is within the range of defined steps to avoid a possible OutOfBoundsException.
	 * @param int $stepNumber
	 * @return int
	 */
	private function ensureStepNumberRange($stepNumber) {
		return max(min($stepNumber, $this->getStepCount()), 1);
	}

	/**
	 * Refines the current step number by evaluating and considering skipped steps.
	 * @param int $refinedStepNumber
	 * @return int
	 */
	protected function refineCurrentStepNumber($refinedStepNumber) {
		foreach ($this->getSteps() as $step) {
			$step->evaluateSkipping($refinedStepNumber, $this);
		}

		return $refinedStepNumber;
	}

	/**
	 * {@inheritDoc}
	 */
	public function bind($formData) {
		$this->setInstanceId($this->determineInstanceId());

		if ($this->hasListeners(FormFlowEvents::PRE_BIND)) {
			$this->dispatchEvent(new PreBindEvent($this), FormFlowEvents::PRE_BIND);
		}

		$this->formData = $formData;

		$this->bindFlow();

		if ($this->hasListeners(FormFlowEvents::POST_BIND_FLOW)) {
			$this->dispatchEvent(new PostBindFlowEvent($this, $this->formData), FormFlowEvents::POST_BIND_FLOW);
		}

		if (!$this->dataManager->exists($this)) {
			// initialize storage slot
			$this->dataManager->save($this, []);
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
		$request = $this->getRequest();
		$reset = false;

		if (!$this->allowDynamicStepNavigation && !$this->allowRedirectAfterSubmit && $request->isMethod('GET')) {
			$reset = true;
		}

		if ($this->getRequestedTransition() === self::TRANSITION_RESET) {
			$reset = true;
		}

		if (in_array($request->getMethod(), ['POST', 'PUT'], true) && $request->get($this->getFormStepKey()) !== null && !$this->dataManager->exists($this)) {
			// flow is expired, drop posted data and reset
			$request->request->replace();
			$reset = true;
			$this->expired = true;

			// Regenerate instance ID so resubmits of the form will continue to give error. Otherwise, submitting
			// the new form, then backing up to the old form won't give the error.
			$this->setInstanceId($this->determineInstanceId());
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

		if (!\class_exists('Symfony\Component\HttpFoundation\InputBag')) {
			// TODO remove as soon as Symfony >= 5.1 is required
			$currentStepData = $request->request->get($formName, []);
		} else {
			$currentStepData = $request->request->all($formName);
		}

		if ($this->handleFileUploads) {
			$currentStepData = array_merge_recursive($currentStepData, $request->files->get($formName, []));
		}

		$stepData[$this->getCurrentStepNumber()] = $currentStepData;

		$this->saveStepData($stepData);
	}

	/**
	 * Invalidates data for steps >= $fromStepNumber.
	 * @param int $fromStepNumber
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

		$this->stepForms = [];

		$options = [];
		if (!$this->revalidatePreviousSteps) {
			$options['validation_groups'] = false; // disable validation
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
					$this->dispatchEvent(new PostBindSavedDataEvent($this, $this->formData, $stepNumber), FormFlowEvents::POST_BIND_SAVED_DATA);
				}
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function createForm() {
		$form = $this->createFormForStep($this->currentStepNumber);

		if ($this->expired && $this->hasListeners(FormFlowEvents::FLOW_EXPIRED)) {
			$this->dispatchEvent(new FlowExpiredEvent($this, $form), FormFlowEvents::FLOW_EXPIRED);
		}

		return $form;
	}

	public function getFormOptions($step, array $options = []) {
		// override options in a specific order
		$options = array_merge(
			$this->getGenericFormOptions(),
			$this->getStep($step)->getFormOptions(),
			$options
		);

		// add the generated step-based validation group, unless it's explicitly set to false, a closure, or a GroupSequence
		if (!array_key_exists('validation_groups', $options)) {
			$options['validation_groups'] = [$this->getValidationGroupPrefix() . $step];
		} else {
			$vg = $options['validation_groups'];

			if ($vg !== false && !is_a($vg, 'Closure') && !$vg instanceof GroupSequence) {
				$options['validation_groups'] = array_merge(
					[$this->getValidationGroupPrefix() . $step],
					(array) $vg
				);
			}
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
			throw new InvalidTypeException($stepNumber, 'int');
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
			$this->dispatchEvent($event, FormFlowEvents::GET_STEPS);

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
			$stepLabels = [];

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

		if (in_array($request->getMethod(), ['POST', 'PUT'], true) && !in_array($this->getRequestedTransition(), [
			self::TRANSITION_BACK,
			self::TRANSITION_RESET,
		], true)) {
			$form->handleRequest($request);

			if (!$form->isSubmitted()) {
				return false;
			}

			if ($this->hasListeners(FormFlowEvents::POST_BIND_REQUEST)) {
				$this->dispatchEvent(new PostBindRequestEvent($this, $form->getData(), $this->currentStepNumber), FormFlowEvents::POST_BIND_REQUEST);
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
							$this->dispatchEvent(new PreviousStepInvalidEvent($this, $form, $stepNumber), FormFlowEvents::PREVIOUS_STEP_INVALID);
						}

						return false;
					}
				}
			}

			if ($form->isValid()) {
				if ($this->hasListeners(FormFlowEvents::POST_VALIDATE)) {
					$this->dispatchEvent(new PostValidateEvent($this, $form->getData()), FormFlowEvents::POST_VALIDATE);
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * @param FormInterface $submittedForm
	 * @return bool If a redirection should be performed.
	 */
	public function redirectAfterSubmit(FormInterface $submittedForm) {
		if ($this->allowRedirectAfterSubmit && in_array($this->getRequest()->getMethod(), ['POST', 'PUT'], true)) {
			switch ($this->getRequestedTransition()) {
				case self::TRANSITION_BACK:
				case self::TRANSITION_RESET:
					return true;
				default:
					// redirect after submit only if there are no errors for the submitted form
					return $submittedForm->isSubmitted() && $submittedForm->isValid();
			}
		}

		return false;
	}

	/**
	 * Creates the form for the given step number.
	 * @param int $stepNumber
	 * @param array $options
	 * @return FormInterface
	 */
	protected function createFormForStep($stepNumber, array $options = []) {
		$formType = $this->getStep($stepNumber)->getFormType();
		$options = $this->getFormOptions($stepNumber, $options);

		if ($formType === null) {
			$formType = FormType::class;
		}

		return $this->formFactory->create($formType, $this->formData, $options);
	}

	/**
	 * Creates all steps from the given configuration.
	 * @param array $stepsConfig
	 * @return StepInterface[] Value with index 0 is step 1.
	 */
	public function createStepsFromConfig(array $stepsConfig) {
		$steps = [];

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
		return [];
	}

	protected function retrieveStepData() {
		return $this->dataManager->load($this);
	}

	protected function saveStepData(array $data) {
		$this->dataManager->save($this, $data);
	}

	/**
	 * @param string $eventName
	 * @return bool
	 */
	protected function hasListeners($eventName) {
		return $this->eventDispatcher !== null && $this->eventDispatcher->hasListeners($eventName);
	}

	/**
	 * @param FormFlowEvent $event
	 * @param string $eventName
	 */
	private function dispatchEvent($event, $eventName) {
		if (Kernel::VERSION_ID < 40300) {
			// TODO remove as soon as Symfony >= 4.3 is required
			$this->eventDispatcher->dispatch($eventName, $event);
		} else {
			$this->eventDispatcher->dispatch($event, $eventName);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStepsDone() {
		$stepsDone = [];

		foreach ($this->getSteps() as $step) {
			if ($this->isStepDone($step->getNumber())) {
				$stepsDone[] = $step;
			}
		}

		return $stepsDone;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStepsRemaining() {
		$stepsRemaining = [];

		foreach ($this->getSteps() as $step) {
			if (!$this->isStepDone($step->getNumber())) {
				$stepsRemaining[] = $step;
			}
		}

		return $stepsRemaining;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStepsDoneCount() {
		return count($this->getStepsDone());
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStepsRemainingCount() {
		return count($this->getStepsRemaining());
	}

	// methods for BC with third-party templates (e.g. MopaBootstrapBundle)

	public function getCurrentStep() {
		@trigger_error('Method ' . __METHOD__ . ' is deprecated since CraueFormFlowBundle 2.0. Use method getCurrentStepNumber instead.', E_USER_DEPRECATED);
		return $this->getCurrentStepNumber();
	}

	public function getCurrentStepDescription() {
		@trigger_error('Method ' . __METHOD__ . ' is deprecated since CraueFormFlowBundle 2.0. Use method getCurrentStepLabel instead.', E_USER_DEPRECATED);
		return $this->getCurrentStepLabel();
	}

	public function getMaxSteps() {
		@trigger_error('Method ' . __METHOD__ . ' is deprecated since CraueFormFlowBundle 2.0. Use method getStepCount instead.', E_USER_DEPRECATED);
		return $this->getStepCount();
	}

	public function getStepDescriptions() {
		@trigger_error('Method ' . __METHOD__ . ' is deprecated since CraueFormFlowBundle 2.0. Use method getStepLabels instead.', E_USER_DEPRECATED);
		return $this->getStepLabels();
	}

	public function getFirstStep() {
		@trigger_error('Method ' . __METHOD__ . ' is deprecated since CraueFormFlowBundle 2.0. Use method getFirstStepNumber instead.', E_USER_DEPRECATED);
		return $this->getFirstStepNumber();
	}

	public function getLastStep() {
		@trigger_error('Method ' . __METHOD__ . ' is deprecated since CraueFormFlowBundle 2.0. Use method getLastStepNumber instead.', E_USER_DEPRECATED);
		return $this->getLastStepNumber();
	}

	public function hasSkipStep($stepNumber) {
		@trigger_error('Method ' . __METHOD__ . ' is deprecated since CraueFormFlowBundle 2.0. Use method isStepSkipped instead.', E_USER_DEPRECATED);
		return $this->isStepSkipped($stepNumber);
	}

}
