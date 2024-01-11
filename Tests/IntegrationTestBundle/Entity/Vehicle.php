<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2024 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Vehicle {

	/**
	 * @var int
	 */
	public $numberOfWheels;

	/**
	 * @var string
	 */
	public $engine;

	public function canHaveEngine() {
		return $this->numberOfWheels === 4;
	}

	public static function loadValidatorMetadata(ClassMetadata $metadata) : void {
		$metadata->addPropertyConstraint('numberOfWheels', new Assert\NotBlank(['groups' => 'flow_createVehicle_step1']));
		$metadata->addPropertyConstraint('engine', new Assert\NotBlank(['groups' => 'flow_createVehicle_step2']));
	}

}
