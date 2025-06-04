<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2024 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class RevalidatePreviousStepsData {

	private static $validationCalls;

	public static function resetValidationCalls() {
		self::$validationCalls = 0;
	}

	public function isDataValid(ExecutionContextInterface $context) {
		// valid only on first call
		if (++self::$validationCalls > 1) {
			$context->addViolation('Take this!');
		}
	}

	public static function loadValidatorMetadata(ClassMetadata $metadata) : void {
		$options = ['groups' => ['flow_revalidatePreviousSteps_step1'], 'callback' => 'isDataValid'];
		$metadata->addConstraint(\version_compare(\PHP_VERSION, '8.0', '<') ? new Callback($options) : new Callback(...$options));
	}

}
