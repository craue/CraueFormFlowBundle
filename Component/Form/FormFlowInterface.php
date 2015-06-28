<?php

namespace Craue\FormFlowBundle\Form;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Craue\FormFlowBundle\Storage\DataManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
interface FormFlowInterface {

	/**
	 * @return string
	 */
	function getName();

	/**
	 * @param FormFactoryInterface $formFactory
	 */
	function setFormFactory(FormFactoryInterface $formFactory);

	/**
	 * @param Request|null $request
	 */
	function setRequest(Request $request = null);

	/**
	 * @param DataManagerInterface $dataManager
	 */
	function setDataManager(DataManagerInterface $dataManager);

	/**
	 * @return DataManagerInterface
	 */
	function getDataManager();

	/**
	 * @param EventDispatcherInterface $eventDispatcher
	 */
	function setEventDispatcher(EventDispatcherInterface $eventDispatcher);

	/**
	 * @return boolean
	 */
	function isRevalidatePreviousSteps();

	/**
	 * @return boolean
	 */
	function isAllowDynamicStepNavigation();

	/**
	 * @return boolean If file uploads should be handled by serializing them into the storage.
	 */
	function isHandleFileUploads();

	/**
	 * @return string|null Directory for storing temporary files while handling uploads. If <code>null</code>, the system's default will be used.
	 */
	function getHandleFileUploadsTempDir();

	/**
	 * @return boolean
	 */
	function isAllowRedirectAfterSubmit();

	/**
	 * @return string
	 */
	function getId();

	/**
	 * @return string
	 */
	function getInstanceId();

	/**
	 * Restores previously saved form data of all steps and determines the current step.
	 * @param mixed $formData
	 */
	function bind($formData);

	/**
	 * @return mixed
	 */
	function getFormData();

	/**
	 * Creates the form for the current step.
	 * @return FormInterface
	 */
	function createForm();

	/**
	 * @param integer $stepNumber
	 * @return boolean
	 */
	function isStepDone($stepNumber);

	/**
	 * @param integer $stepNumber
	 * @return boolean
	 */
	function isStepSkipped($stepNumber);

	/**
	 * @param FormInterface $form
	 * @return boolean Whether the form is valid.
	 */
	function isValid(FormInterface $form);

	/**
	 * Saves the form data of the current step.
	 * @param FormInterface $form
	 */
	function saveCurrentStepData(FormInterface $form);

	/**
	 * Proceeds to the next step.
	 * @return boolean Whether the next step can be prepared. If not, the flow is finished.
	 */
	function nextStep();

	/**
	 * Resets the flow and clears its underlying storage.
	 */
	function reset();

	/**
	 * @return integer First visible step, which may be greater than 1 if steps are skipped.
	 */
	function getFirstStepNumber();

	/**
	 * @return integer Last visible step, which may be less than <code>getStepCount()</code> if steps are skipped.
	 */
	function getLastStepNumber();

	/**
	 * @return integer
	 * @throws \RuntimeException If the current step is not yet known.
	 */
	function getCurrentStepNumber();

	/**
	 * @return string|null The label for the current step.
	 */
	function getCurrentStepLabel();

	/**
	 * Get labels for all steps used to render the step list.
	 * @return string[]|null[] Value with index 0 is the label for step 1.
	 */
	function getStepLabels();

	/**
	 * @param integer $stepNumber
	 * @return StepInterface
	 * @throws InvalidTypeException If <code>$stepNumber</code> is not an integer.
	 * @throws \OutOfBoundsException If step <code>$stepNumber</code> doesn't exist.
	 */
	function getStep($stepNumber);

	/**
	 * @return StepInterface[] Value with index 0 is step 1.
	 */
	function getSteps();

	/**
	 * @return integer
	 */
	function getStepCount();

}
