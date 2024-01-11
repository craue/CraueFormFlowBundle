<?php

namespace Craue\FormFlowBundle\EventListener;

use Craue\FormFlowBundle\Event\PreviousStepInvalidEvent;
use Symfony\Component\Form\FormError;

/**
 * Adds a validation error to the current step's form if revalidating previous steps failed.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2024 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PreviousStepInvalidEventListener {

	use EventListenerWithTranslatorTrait;

	public function onPreviousStepInvalid(PreviousStepInvalidEvent $event) {
		$event->getCurrentStepForm()->addError($this->getPreviousStepInvalidFormError($event->getInvalidStepNumber()));
	}

	/**
	 * @param int $stepNumber
	 * @return FormError
	 */
	protected function getPreviousStepInvalidFormError($stepNumber) {
		$messageId = 'craueFormFlow.previousStepInvalid';
		$messageParameters = ['%stepNumber%' => $stepNumber];

		return new FormError($this->translator->trans($messageId, $messageParameters, 'validators'), $messageId, $messageParameters);
	}

}
