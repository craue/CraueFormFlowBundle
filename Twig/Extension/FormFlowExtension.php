<?php

namespace Craue\FormFlowBundle\Twig\Extension;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Util\FormFlowUtil;

/**
 * Twig extension for form flows.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowExtension extends \Twig_Extension {

	/**
	 * @var FormFlowUtil
	 */
	protected $formFlowUtil;

	public function setFormFlowUtil(FormFlowUtil $formFlowUtil) {
		$this->formFlowUtil = $formFlowUtil;
	}

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
			new \Twig_SimpleFilter('craue_addDynamicStepNavigationParameters', array($this, 'addDynamicStepNavigationParameters')),
			new \Twig_SimpleFilter('craue_removeDynamicStepNavigationParameters', array($this, 'removeDynamicStepNavigationParameters')),
			// methods for BC with third-party templates (e.g. MopaBootstrapBundle)
			new \Twig_SimpleFilter('craue_addDynamicStepNavigationParameter', array($this, 'addDynamicStepNavigationParameter')),
			new \Twig_SimpleFilter('craue_removeDynamicStepNavigationParameter', array($this, 'removeDynamicStepNavigationParameter')),
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
	 * Adds route parameters for dynamic step navigation.
	 * @param array $parameters Current route parameters.
	 * @param FormFlow $flow The flow involved.
	 * @param int $stepNumber Number of the step the link will be generated for.
	 * @return array Route parameters plus instance and step parameter.
	 */
	public function addDynamicStepNavigationParameters(array $parameters, FormFlow $flow, $stepNumber) {
		return $this->formFlowUtil->addRouteParameters($parameters, $flow, $stepNumber);
	}

	/**
	 * Removes route parameters for dynamic step navigation.
	 * @param array $parameters Current route parameters.
	 * @param FormFlow $flow The flow involved.
	 * @return array Route parameters without instance and step parameter.
	 */
	public function removeDynamicStepNavigationParameters(array $parameters, FormFlow $flow) {
		return $this->formFlowUtil->removeRouteParameters($parameters, $flow);
	}

	/**
	 * @param FormFlow $flow The flow involved.
	 * @param int $stepNumber Number of the step the link will be generated for.
	 * @return bool If the step can be linked to.
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

	// methods for BC with third-party templates (e.g. MopaBootstrapBundle)

	public function addDynamicStepNavigationParameter(array $parameters, FormFlow $flow, $stepNumber) {
		@trigger_error('Twig filter craue_addDynamicStepNavigationParameter is deprecated since version 3.0. Use filter craue_addDynamicStepNavigationParameters instead.', E_USER_DEPRECATED);
		return $this->addDynamicStepNavigationParameters($parameters, $flow, $stepNumber);
	}

	public function removeDynamicStepNavigationParameter(array $parameters, FormFlow $flow) {
		@trigger_error('Twig filter craue_removeDynamicStepNavigationParameter is deprecated since version 3.0. Use filter craue_removeDynamicStepNavigationParameters instead.', E_USER_DEPRECATED);
		return $this->removeDynamicStepNavigationParameters($parameters, $flow);
	}

}
