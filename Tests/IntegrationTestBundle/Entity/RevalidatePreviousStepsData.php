<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
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

	// TODO replace by a proper annotation on class-level as soon as Symfony >= 2.4 is required
	public static function loadValidatorMetadata(ClassMetadata $metadata) {
		$callbackConstraintOptions = array(
			'groups' => 'flow_revalidatePreviousSteps_step1',
		);

		if (Kernel::VERSION_ID < 20400) {
			$callbackConstraintOptions['methods'] = array('isDataValid');
		} else {
			$callbackConstraintOptions['callback'] = 'isDataValid';
		}

		$metadata->addConstraint(new Assert\Callback($callbackConstraintOptions));
	}

}
