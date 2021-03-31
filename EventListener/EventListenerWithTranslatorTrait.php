<?php

namespace Craue\FormFlowBundle\EventListener;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2021 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
trait EventListenerWithTranslatorTrait {

	/**
	 * @var TranslatorInterface|LegacyTranslatorInterface
	 */
	protected $translator;

	/**
	 * @param TranslatorInterface|LegacyTranslatorInterface $translator
	 * @throws InvalidTypeException
	 */
	public function setTranslator($translator) {
		// TODO revert to type-hint with only TranslatorInterface as soon as Symfony >= 5.0 is required
		if ($translator instanceof TranslatorInterface || $translator instanceof LegacyTranslatorInterface) {
			$this->translator = $translator;

			return;
		}

		throw new InvalidTypeException($translator, [TranslatorInterface::class, LegacyTranslatorInterface::class]);
	}

}
