<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2025 Christian Raue
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
		$titleNotBlankOptions = ['groups' => ['flow_createTopic_step1']];
		$metadata->addPropertyConstraint('title', \version_compare(\PHP_VERSION, '8.0', '<') ? new NotBlank($titleNotBlankOptions) : new NotBlank(...$titleNotBlankOptions));
		$categoryChoiceOptions = ['groups' => ['flow_createTopic_step1'], 'callback' => 'getValidCategories', 'strict' => true];
		$metadata->addPropertyConstraint('category', \version_compare(\PHP_VERSION, '8.0', '<') ? new Choice($categoryChoiceOptions) : new Choice(...$categoryChoiceOptions));
		$categoryNotBlankOptions = ['groups' => ['flow_createTopic_step1']];
		$metadata->addPropertyConstraint('category', \version_compare(\PHP_VERSION, '8.0', '<') ? new NotBlank($categoryNotBlankOptions) : new NotBlank(...$categoryNotBlankOptions));
		$detailsNotBlankOptions = ['groups' => ['flow_createTopic_step3']];
		$metadata->addPropertyConstraint('details', \version_compare(\PHP_VERSION, '8.0', '<') ? new NotBlank($detailsNotBlankOptions) : new NotBlank(...$detailsNotBlankOptions));
	}

}
