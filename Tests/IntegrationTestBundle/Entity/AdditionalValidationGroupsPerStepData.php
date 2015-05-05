<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class AdditionalValidationGroupsPerStepData {

	/**
	 * @var string
	 * @Assert\NotBlank(groups={"flow_additionalValidationGroupsPerStep_step1", "additionalValidationGroupsPerStep2", "additionalValidationGroupsPerStep3a"})
	 * @Assert\Regex(pattern="/^[[:alpha:]]*$/", groups={"additionalValidationGroupsPerStep3b"})
	 */
	public $value;

}
