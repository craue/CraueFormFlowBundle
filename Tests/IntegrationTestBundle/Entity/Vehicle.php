<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints\NotBlank;
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
		$numberOfWheelsNotBlankOptions = ['groups' => ['flow_createVehicle_step1']];
		$metadata->addPropertyConstraint('numberOfWheels', \version_compare(\PHP_VERSION, '8.0', '<') ? new NotBlank($numberOfWheelsNotBlankOptions) : new NotBlank(...$numberOfWheelsNotBlankOptions));
		$engineNotBlankOptions = ['groups' => ['flow_createVehicle_step2']];
		$metadata->addPropertyConstraint('engine', \version_compare(\PHP_VERSION, '8.0', '<') ? new NotBlank($engineNotBlankOptions) : new NotBlank(...$engineNotBlankOptions));
	}

}
