<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2024 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue64Data {

	/**
	 * @var Issue64SubData
	 */
	public $sub;

	public static function loadValidatorMetadata(ClassMetadata $metadata) : void {
		$metadata->addPropertyConstraint('sub', new Assert\NotNull(['groups' => ['flow_issue64_step1', 'flow_issue64_step2', 'flow_issue64_step3']]));
		$metadata->addPropertyConstraint('sub', new Assert\Valid(['groups' => ['flow_issue64_step1', 'flow_issue64_step2', 'flow_issue64_step3']]));
	}

}
