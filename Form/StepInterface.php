<?php

namespace Craue\FormFlowBundle\Form;

use Symfony\Component\Form\FormTypeInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
interface StepInterface {

	/**
	 * @return int
	 */
	function getNumber();

	/**
	 * @return string|null
	 */
	function getLabel();

	/**
	 * @return FormTypeInterface|string|null
	 */
	function getFormType();

	/**
	 * @return array
	 */
	function getFormOptions();

	/**
	 * @return bool
	 */
	function isSkipped();

	/**
	 * @param int $estimatedCurrentStepNumber
	 * @param FormFlowInterface $flow
	 */
	function evaluateSkipping($estimatedCurrentStepNumber, FormFlowInterface $flow);

}
