<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue149SubData {

	/**
	 * @var UploadedFile
	 */
	public $image;

	/**
	 * @var string
	 */
	public $title;

}
