<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2016 Christian Raue
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
	// TODO should be type-hinted with Symfony\Component\Validator\Context\ExecutionContextInterface, but tests fail with Symfony 2.6.4 and PHP 5.3.3, see https://travis-ci.org/craue/CraueFormFlowBundle/jobs/94383918#L249
	public function isDataValid($context) {
		// valid only on first call
		if (++self::$validationCalls > 1) {
			$context->addViolation('Take this!');
		}
	}

}
