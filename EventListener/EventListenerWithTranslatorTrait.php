<?php

namespace Craue\FormFlowBundle\EventListener;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2024 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
trait EventListenerWithTranslatorTrait {

	/**
	 * @var TranslatorInterface
	 */
	protected $translator;

	/**
	 * @param TranslatorInterface $translator
	 */
	public function setTranslator(TranslatorInterface $translator) {
		$this->translator = $translator;
	}

}
