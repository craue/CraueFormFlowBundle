<?php

namespace Craue\FormFlowBundle\Tests\EventListener;

use Craue\FormFlowBundle\Tests\UnitTestCase;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2019 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class EventListenerNeedsTranslatorTest extends UnitTestCase {

	abstract protected function getListener();

	/**
	 * TranslatorInterface (from contracts) was introduced with Symfony 4.2, but could be installed separately by symfony/translation-contracts along with Symfony < 4.2.
	 *
	 * @dataProvider dataSetTranslator
	 * @doesNotPerformAssertions
	 */
	public function testSetTranslator($translator) {
		$this->getListener()->setTranslator($translator);
	}

	public function dataSetTranslator() {
		$translators = [
			[$this->createMock(DataCollectorTranslator::class)],
		];

		if (interface_exists(LegacyTranslatorInterface::class)) {
			// TODO remove as soon as Symfony >= 4.2 is required
			$translators[] = [$this->createMock(LegacyTranslatorInterface::class)];
		} else {
			$translators[] = [$this->createMock(TranslatorInterface::class)];
		}

		return $translators;
	}

}
