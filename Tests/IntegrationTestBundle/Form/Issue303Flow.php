<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue303Flow extends FormFlow {

	private static $skips;

	public static function resetSkips() {
		self::$skips = [];
	}

	public static function setSkip($stepNumber) {
		self::$skips[$stepNumber] = true;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		$steps = [
			1 => [
				'label' => 'step1',
			],
			2 => [
				'label' => 'step2',
			],
		];

		foreach (self::$skips as $stepNumber => $skip) {
			$steps[$stepNumber]['skip'] = $skip;
		}

		return $steps;
	}

}
