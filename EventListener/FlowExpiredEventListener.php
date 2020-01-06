<?php

namespace Craue\FormFlowBundle\EventListener;

use Craue\FormFlowBundle\Event\FlowExpiredEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 */
abstract class BaseFlowExpiredEventListener {

	/**
	 * @var TranslatorInterface|LegacyTranslatorInterface
	 */
	protected $translator;

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

// TODO revert to one clean class definition as soon as Symfony >= 4.2 is required
if (!interface_exists(LegacyTranslatorInterface::class)) {
	/**
	 * Adds a validation error to the current step's form if an expired flow is detected.
	 *
	 * @author Tim Behrendsen <tim@siliconengine.com>
	 * @copyright 2011-2020 Christian Raue
	 * @license http://opensource.org/licenses/mit-license.php MIT License
	 */
	class FlowExpiredEventListener extends BaseFlowExpiredEventListener {
		public function setTranslator(TranslatorInterface $translator) {
			$this->translator = $translator;
		}
	}
} else {
	/**
	 * Adds a validation error to the current step's form if an expired flow is detected.
	 *
	 * @author Tim Behrendsen <tim@siliconengine.com>
	 * @copyright 2011-2020 Christian Raue
	 * @license http://opensource.org/licenses/mit-license.php MIT License
	 */
	class FlowExpiredEventListener extends BaseFlowExpiredEventListener {
		public function setTranslator(LegacyTranslatorInterface $translator) {
			$this->translator = $translator;
		}
	}
}
