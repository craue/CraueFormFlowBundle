<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2021 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PhotoCollection
{

	public $photos;

	/**
	 * @var string
	 */
	public $comment;

	/**
	 * PhotoCollection constructor.
	 */
	public function __construct()
	{
		$this->photos = new ArrayCollection([["photo" => null, "comment" => null]]);
	}

}
