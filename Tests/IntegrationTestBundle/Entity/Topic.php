<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2023 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Topic {

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var string
	 */
	public $category;

	/**
	 * @var string
	 */
	public $comment;

	/**
	 * @var string
	 */
	public $details;

	public function isBugReport() {
		return $this->category === 'BUG_REPORT';
	}

	public static function getValidCategories() {
		return [
			'DISCUSSION',
			'BUG_REPORT',
			'SUPPORT_REQUEST',
		];
	}

	public static function loadValidatorMetadata(ClassMetadata $metadata) : void {
		$metadata->addPropertyConstraint('title', new Assert\NotBlank(['groups' => 'flow_createTopic_step1']));
		$metadata->addPropertyConstraint('category', new Assert\Choice(['groups' => 'flow_createTopic_step1', 'callback' => 'getValidCategories', 'strict' => true]));
		$metadata->addPropertyConstraint('category', new Assert\NotBlank(['groups' => 'flow_createTopic_step1']));
		$metadata->addPropertyConstraint('details', new Assert\NotBlank(['groups' => 'flow_createTopic_step3']));
	}

}
