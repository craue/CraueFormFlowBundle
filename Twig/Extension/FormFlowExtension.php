<?php

namespace Craue\FormFlowBundle\Twig\Extension;

use Craue\FormFlowBundle\Form\FormFlow;

/**
 * Twig extension for form flows.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2013 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowExtension extends \Twig_Extension {

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'craue_formflow';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFilters() {
		return array(
			'craue_addDynamicStepNavigationParameter' =>
					new \Twig_Filter_Method($this, 'addDynamicStepNavigationParameter'),
			'craue_removeDynamicStepNavigationParameter' =>
					new \Twig_Filter_Method($this, 'removeDynamicStepNavigationParameter'),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFunctions() {
		return array(
			'craue_isStepLinkable' => new \Twig_Function_Method($this, 'isStepLinkable'),
		);
	}

	/**
	 * Adds the parameter for dynamic step navigation.
	 * @param array $parameters Current route parameters.
	 * @param FormFlow $flow The flow involved.
	 * @param integer $stepNumber Number of the step the link will be generated for.
	 * @return array Route parameters plus the step parameter.
	 */
	public function addDynamicStepNavigationParameter(array $parameters, FormFlow $flow, $stepNumber) {
		$parameters[$flow->getDynamicStepNavigationParameter()] = $stepNumber;
		return $parameters;
	}

	/**
	 * Removes the parameter for dynamic step navigation.
	 * @param array $parameters Current route parameters.
	 * @param FormFlow $flow The flow involved.
	 * @return array Route parameters without the step parameter.
	 */
	public function removeDynamicStepNavigationParameter(array $parameters, FormFlow $flow) {
		unset($parameters[$flow->getDynamicStepNavigationParameter()]);
		return $parameters;
	}

	/**
	 * @param FormFlow $flow The flow involved.
	 * @param integer $stepNumber Number of the step the link will be generated for.
	 * @return boolean If the step can be linked to.
	 */
	public function isStepLinkable(FormFlow $flow, $stepNumber) {
		return $flow->isAllowDynamicStepNavigation()
				&& $stepNumber !== $flow->getCurrentStepNumber()
				&& ($flow->isStepDone($stepNumber) || $flow->isStepDone($stepNumber - 1))
				&& !$flow->isStepSkipped($stepNumber);
	}

}
