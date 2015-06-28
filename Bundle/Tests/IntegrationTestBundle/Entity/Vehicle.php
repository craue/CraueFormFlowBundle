<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Vehicle {

	/**
	 * @var integer
	 * @Assert\NotBlank(groups={"flow_createVehicle_step1"})
	 */
	public $numberOfWheels;

	/**
	 * @var string
	 * @Assert\NotBlank(groups={"flow_createVehicle_step2"})
	 */
	public $engine;

	public function canHaveEngine() {
		return $this->numberOfWheels === 4;
	}

}
