<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue149Data {

	/**
	 * @var Issue149SubData
	 * @Assert\Valid(groups={"flow_issue149_step1"})
	 */
	public $photo;

}
