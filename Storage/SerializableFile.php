<?php

namespace Craue\FormFlowBundle\Storage;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Craue\FormFlowBundle\Util\TempFileUtil;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Representation of a serializable file. Only supports <code>UploadedFile</code> currently.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class SerializableFile {

	/**
	 * @var string Base64-encoded content of the original file.
	 */
	protected $content;

	/**
	 * @var string FQCN of the object encapsulating the original file. Not used yet, but meant for possible future support of further types.
	 */
	protected $type;

	protected $clientOriginalName;
	protected $clientMimeType;
	protected $clientSize;

	/**
	 * @param mixed $file An object meant to be serialized.
	 * @throws InvalidTypeException If the type of <code>$file</code> is unsupported.
	 */
	public function __construct($file) {
		if (!self::isSupported($file)) {
			throw new InvalidTypeException($file, 'Symfony\Component\HttpFoundation\File\UploadedFile');
		}

		$this->content = base64_encode(file_get_contents($file->getPathname()));
		$this->type = 'Symfony\Component\HttpFoundation\File\UploadedFile';

		$this->clientOriginalName = $file->getClientOriginalName();
		$this->clientMimeType = $file->getClientMimeType();
		$this->clientSize = $file->getClientSize();
	}

	/**
	 * @param string|null $tempDir Directory for storing temporary files. If <code>null</code>, the system's default will be used.
	 * @return mixed The unserialized object.
	 */
	public function getAsFile($tempDir = null) {
		if ($tempDir === null) {
			$tempDir = sys_get_temp_dir();
		}

		// create a temporary file with its original content
		$tempFile = tempnam($tempDir, 'craue_form_flow_serialized_file');
		file_put_contents($tempFile, base64_decode($this->content));

		TempFileUtil::addTempFile($tempFile);

		return new UploadedFile($tempFile, $this->clientOriginalName, $this->clientMimeType, $this->clientSize, null, true);
	}

	/**
	 * @param mixed $file
	 * @return bool
	 */
	public static function isSupported($file) {
		return $file instanceof UploadedFile;
	}

}
