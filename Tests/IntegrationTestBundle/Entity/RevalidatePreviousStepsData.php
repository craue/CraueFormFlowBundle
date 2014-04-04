<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 *
 * @Assert\Callback(methods={"isDataValid"}, groups={"flow_revalidatePreviousSteps_step1"})
 */
class RevalidatePreviousStepsData {

	private static $validationCalls;

	public static function resetValidationCalls() {
		self::$validationCalls = 0;
	}

	// TODO add Symfony\Component\Validator\ExecutionContextInterface type hint as soon as Symfony >= 2.2 is required
	public function isDataValid($context) {
		// valid only on first call
		if (++self::$validationCalls > 1) {
			$context->addViolation('Take this!');
		}
	}

}
