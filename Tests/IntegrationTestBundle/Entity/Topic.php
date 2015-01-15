<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Topic {

	/**
	 * @var string
	 * @Assert\NotBlank(groups={"flow_createTopic_step1"})
	 */
	public $title;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var string
	 * @Assert\Choice(callback="getValidCategories", groups={"flow_createTopic_step1"})
	 * @Assert\NotBlank(groups={"flow_createTopic_step1"})
	 */
	public $category;

	/**
	 * @var string
	 */
	public $comment;

	/**
	 * @var string
	 * @Assert\NotBlank(groups={"flow_createTopic_step3"})
	 */
	public $details;

	public function isBugReport() {
		return $this->category === 'BUG_REPORT';
	}

	public static function getValidCategories() {
		return array(
			'DISCUSSION',
			'BUG_REPORT',
			'SUPPORT_REQUEST',
		);
	}

}
