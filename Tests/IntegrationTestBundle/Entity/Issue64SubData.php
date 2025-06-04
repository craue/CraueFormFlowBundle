<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2024 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue64SubData {

	/**
	 * @var string
	 */
	public $prop1;

	/**
	 * @var string
	 */
	public $prop2;

	public static function loadValidatorMetadata(ClassMetadata $metadata) : void {
		$prop1NotBlankOptions = ['groups' => ['flow_issue64_step1']];
		$prop2NotBlankOptions = ['groups' => ['flow_issue64_step2']];
		$metadata->addPropertyConstraint('prop1', \version_compare(\PHP_VERSION, '8.0', '<') ? new NotBlank($prop1NotBlankOptions) : new NotBlank(...$prop1NotBlankOptions));
		$metadata->addPropertyConstraint('prop2', \version_compare(\PHP_VERSION, '8.0', '<') ? new NotBlank($prop2NotBlankOptions) : new NotBlank(...$prop2NotBlankOptions));
	}

}
