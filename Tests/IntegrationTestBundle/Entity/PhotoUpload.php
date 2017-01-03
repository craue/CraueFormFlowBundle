<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PhotoUpload {

	/**
	 * @var UploadedFile
	 * @Assert\NotNull(groups={"flow_photoUpload_step1"})
	 * @Assert\Image(groups={"flow_photoUpload_step1"})
	 */
	public $photo;

	/**
	 * @var string
	 */
	public $comment;

	public function getPhotoDataBase64Encoded() {
		return base64_encode(file_get_contents($this->photo->getPathname()));
	}

	public function getPhotoMimeType() {
		return $this->photo->getMimeType();
	}

}
