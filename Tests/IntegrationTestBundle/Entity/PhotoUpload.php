<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2023 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PhotoUpload {

	/**
	 * @var UploadedFile
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

	public static function loadValidatorMetadata(ClassMetadata $metadata) : void {
		$metadata->addPropertyConstraint('photo', new Assert\NotNull(['groups' => 'flow_photoUpload_step1']));
		$metadata->addPropertyConstraint('photo', new Assert\Image(['groups' => 'flow_photoUpload_step1']));
	}

}
