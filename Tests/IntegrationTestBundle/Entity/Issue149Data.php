<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2024 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue149Data {

	/**
	 * @var Issue149SubData
	 */
	public $photo;

	public static function loadValidatorMetadata(ClassMetadata $metadata) : void {
		$options = ['groups' => ['flow_issue149_step1']];
		$metadata->addPropertyConstraint('photo', \version_compare(\PHP_VERSION, '8.0', '<') ? new Valid($options) : new Valid(...$options));
	}

}
