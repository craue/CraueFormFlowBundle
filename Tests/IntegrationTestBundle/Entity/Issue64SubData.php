<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2023 Christian Raue
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
		$metadata->addPropertyConstraint('prop1', new Assert\NotBlank(['groups' => 'flow_issue64_step1']));
		$metadata->addPropertyConstraint('prop2', new Assert\NotBlank(['groups' => 'flow_issue64_step2']));
	}

}
