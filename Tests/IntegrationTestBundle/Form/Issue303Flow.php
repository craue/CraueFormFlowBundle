<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2018 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue303Flow extends FormFlow {

	private static $skips;

	public static function resetSkips() {
		self::$skips = array();
	}

	public static function setSkip($stepNumber) {
		self::$skips[$stepNumber] = true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'issue303';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		$steps = array(
			1 => array(
				'label' => 'step1',
			),
			2 => array(
				'label' => 'step2',
			),
		);

		foreach (self::$skips as $stepNumber => $skip) {
			$steps[$stepNumber]['skip'] = $skip;
		}

		return $steps;
	}

}
