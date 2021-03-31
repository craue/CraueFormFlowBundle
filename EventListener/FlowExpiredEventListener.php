<?php

namespace Craue\FormFlowBundle\EventListener;

use Craue\FormFlowBundle\Event\FlowExpiredEvent;
use Symfony\Component\Form\FormError;

/**
 * Adds a validation error to the current step's form if an expired flow is detected.
 *
 * @author Tim Behrendsen <tim@siliconengine.com>
 * @copyright 2011-2021 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FlowExpiredEventListener {

	use EventListenerWithTranslatorTrait;

	public function onFlowExpired(FlowExpiredEvent $event) {
		$event->getCurrentStepForm()->addError($this->getFlowExpiredFormError());
	}

	/**
	 * @return FormError
	 */
	protected function getFlowExpiredFormError() {
		$messageId = 'craueFormFlow.flowExpired';
		$messageParameters = [];

		return new FormError($this->translator->trans($messageId, $messageParameters, 'validators'), $messageId, $messageParameters);
	}

}
