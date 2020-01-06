<?php

namespace Craue\FormFlowBundle\EventListener;

use Craue\FormFlowBundle\Event\PreviousStepInvalidEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 */
abstract class BasePreviousStepInvalidEventListener {

	/**
	 * @var TranslatorInterface|LegacyTranslatorInterface
	 */
	protected $translator;

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

// TODO revert to one clean class definition as soon as Symfony >= 4.2 is required
if (!interface_exists(LegacyTranslatorInterface::class)) {
	/**
	 * Adds a validation error to the current step's form if revalidating previous steps failed.
	 *
	 * @author Christian Raue <christian.raue@gmail.com>
	 * @copyright 2011-2020 Christian Raue
	 * @license http://opensource.org/licenses/mit-license.php MIT License
	 */
	class PreviousStepInvalidEventListener extends BasePreviousStepInvalidEventListener {
		public function setTranslator(TranslatorInterface $translator) {
			$this->translator = $translator;
		}
	}
} else {
	/**
	 * Adds a validation error to the current step's form if revalidating previous steps failed.
	 *
	 * @author Christian Raue <christian.raue@gmail.com>
	 * @copyright 2011-2020 Christian Raue
	 * @license http://opensource.org/licenses/mit-license.php MIT License
	 */
	class PreviousStepInvalidEventListener extends BasePreviousStepInvalidEventListener {
		public function setTranslator(LegacyTranslatorInterface $translator) {
			$this->translator = $translator;
		}
	}
}
