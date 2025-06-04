<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2025 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue64Data {

	/**
	 * @var Issue64SubData
	 */
	public $sub;

	public static function loadValidatorMetadata(ClassMetadata $metadata) : void {
		$options = ['groups' => ['flow_issue64_step1', 'flow_issue64_step2', 'flow_issue64_step3']];
		$metadata->addPropertyConstraint('sub', \version_compare(\PHP_VERSION, '8.0', '<') ? new NotNull($options) : new NotNull(...$options));
		$metadata->addPropertyConstraint('sub', \version_compare(\PHP_VERSION, '8.0', '<') ? new Valid($options) : new Valid(...$options));
	}

}
