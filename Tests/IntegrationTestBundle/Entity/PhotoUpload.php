<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2024 Christian Raue
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
		$options = ['groups' => ['flow_photoUpload_step1']];
		$metadata->addPropertyConstraint('photo', \version_compare(\PHP_VERSION, '8.0', '<') ? new NotNull($options) : new NotNull(...$options));
		$metadata->addPropertyConstraint('photo', \version_compare(\PHP_VERSION, '8.0', '<') ? new Image($options) : new Image(...$options));
	}

}
