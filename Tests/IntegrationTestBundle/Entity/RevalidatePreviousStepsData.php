<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class RevalidatePreviousStepsData {

	private static $validationCalls;

	public static function resetValidationCalls() {
		self::$validationCalls = 0;
	}

	/**
	 * @Assert\Callback(groups={"flow_revalidatePreviousSteps_step1"})
	 */
	public function isDataValid(ExecutionContextInterface $context) {
		// valid only on first call
		if (++self::$validationCalls > 1) {
			$context->addViolation('Take this!');
		}
	}

}
