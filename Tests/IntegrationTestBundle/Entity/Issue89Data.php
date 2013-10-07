<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2013 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue89Data {

	/**
	 * @var string
	 * @Assert\NotBlank(groups={"flow_issue89_step1"})
	 */
	public $prop1;

	/**
	 * @var string
	 * @Assert\NotBlank(groups={"flow_issue89_step1"})
	 */
	public $prop2;

}
