<?php

namespace Craue\FormFlowBundle\Form;

use Symfony\Component\Form\FormTypeInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2013 Christian Raue
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
	function getType();

	/**
	 * @return boolean
	 */
	function isSkipped();

	/**
	 * @param integer $estimatedCurrentStepNumber
	 * @param FormFlowInterface $flow
	 */
	function evaluateSkipping($estimatedCurrentStepNumber, FormFlowInterface $flow);

	/**
	 * @param string $template
	 * @return StepInterface
	 */
	function setTemplate($template);

	/**
	 * @return string
	 */
	function getTemplate();
}
