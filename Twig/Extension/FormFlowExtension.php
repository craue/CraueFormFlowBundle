<?php

namespace Craue\FormFlowBundle\Twig\Extension;

use Craue\FormFlowBundle\Form\FormFlow;

/**
 * Twig extension for form flows.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
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
			new \Twig_SimpleFilter('craue_addDynamicStepNavigationParameters',
					array($this, 'addDynamicStepNavigationParameters')),
			new \Twig_SimpleFilter('craue_removeDynamicStepNavigationParameters',
					array($this, 'removeDynamicStepNavigationParameters')),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFunctions() {
		return array(
			new \Twig_SimpleFunction('craue_isStepLinkable', array($this, 'isStepLinkable')),
		);
	}

	/**
	 * Adds parameters for dynamic step navigation.
	 * @param array $parameters Current route parameters.
	 * @param FormFlow $flow The flow involved.
	 * @param integer $stepNumber Number of the step the link will be generated for.
	 * @return array Route parameters plus instance and step parameter.
	 */
	public function addDynamicStepNavigationParameters(array $parameters, FormFlow $flow, $stepNumber) {
		$parameters[$flow->getDynamicStepNavigationInstanceParameter()] = $flow->getInstanceId();
		$parameters[$flow->getDynamicStepNavigationStepParameter()] = $stepNumber;

		return $parameters;
	}

	/**
	 * Removes parameters for dynamic step navigation.
	 * @param array $parameters Current route parameters.
	 * @param FormFlow $flow The flow involved.
	 * @return array Route parameters without instance and step parameter.
	 */
	public function removeDynamicStepNavigationParameters(array $parameters, FormFlow $flow) {
		unset($parameters[$flow->getDynamicStepNavigationInstanceParameter()]);
		unset($parameters[$flow->getDynamicStepNavigationStepParameter()]);

		return $parameters;
	}

	/**
	 * @param FormFlow $flow The flow involved.
	 * @param integer $stepNumber Number of the step the link will be generated for.
	 * @return boolean If the step can be linked to.
	 */
	public function isStepLinkable(FormFlow $flow, $stepNumber) {
		if (!$flow->isAllowDynamicStepNavigation()
				|| $flow->getCurrentStepNumber() === $stepNumber
				|| $flow->isStepSkipped($stepNumber)) {
			return false;
		}

		$lastStepConsecutivelyDone = 0;
		for ($i = $flow->getFirstStepNumber(), $lastStepNumber = $flow->getLastStepNumber(); $i < $lastStepNumber; ++$i) {
			if ($flow->isStepDone($i)) {
				$lastStepConsecutivelyDone = $i;
			} else {
				break;
			}
		}

		$lastStepLinkable = $lastStepConsecutivelyDone + 1;

		if ($stepNumber <= $lastStepLinkable) {
			return true;
		}

		return false;
	}

}
