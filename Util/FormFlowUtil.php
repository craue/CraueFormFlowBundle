<?php

namespace Craue\FormFlowBundle\Util;

use Craue\FormFlowBundle\Form\FormFlow;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowUtil {

	/**
	 * Adds route parameters for dynamic step navigation.
	 * @param array $parameters Current route parameters.
	 * @param FormFlow $flow The flow involved.
	 * @param int|null $stepNumber Number of the step the link will be generated for. If <code>null</code>, the <code>$flow</code>'s current step number will be used.
	 * @return array Route parameters plus instance and step parameter.
	 */
	public function addRouteParameters(array $parameters, FormFlow $flow, $stepNumber = null) {
		if ($stepNumber === null) {
			$stepNumber = $flow->getCurrentStepNumber();
		}

		$parameters[$flow->getDynamicStepNavigationInstanceParameter()] = $flow->getInstanceId();
		$parameters[$flow->getDynamicStepNavigationStepParameter()] = $stepNumber;

		return $parameters;
	}

	/**
	 * Removes route parameters for dynamic step navigation.
	 * @param array $parameters Current route parameters.
	 * @param FormFlow $flow The flow involved.
	 * @return array Route parameters without instance and step parameter.
	 */
	public function removeRouteParameters(array $parameters, FormFlow $flow) {
		unset($parameters[$flow->getDynamicStepNavigationInstanceParameter()]);
		unset($parameters[$flow->getDynamicStepNavigationStepParameter()]);

		return $parameters;
	}

}
