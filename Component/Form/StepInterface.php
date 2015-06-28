<?php

namespace Craue\FormFlowBundle\Form;

use Symfony\Component\Form\FormTypeInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
interface StepInterface {

	/**
	 * @return integer
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
	 * @return boolean
	 */
	function isSkipped();

	/**
	 * @param integer $estimatedCurrentStepNumber
	 * @param FormFlowInterface $flow
	 */
	function evaluateSkipping($estimatedCurrentStepNumber, FormFlowInterface $flow);

}
