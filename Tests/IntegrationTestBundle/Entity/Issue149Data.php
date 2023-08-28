<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2023 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue149Data {

	/**
	 * @var Issue149SubData
	 */
	public $photo;

	public static function loadValidatorMetadata(ClassMetadata $metadata) : void {
		$metadata->addPropertyConstraint('photo', new Assert\Valid(['groups' => 'flow_issue149_step1']));
	}

}
