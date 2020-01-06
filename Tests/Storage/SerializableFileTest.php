<?php

namespace Craue\FormFlowBundle\Tests\Storage;

use Craue\FormFlowBundle\Storage\SerializableFile;
use Craue\FormFlowBundle\Util\TempFileUtil;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class SerializableFileTest extends TestCase {

	const DOCUMENT = '/../Fixtures/some-text.txt';

	private $tempFolder = null;

	protected function tearDown() {
		TempFileUtil::removeTempFiles();

		if ($this->tempFolder !== null && is_dir($this->tempFolder)) {
			rmdir($this->tempFolder);
		}
	}

	public function testSerialization() {
		$document = __DIR__ . self::DOCUMENT;
		$originalName = basename($document);
		$mimeType = 'application/octet-stream';

		$serializableFile = new SerializableFile($this->getNewUploadedFile($document, $originalName, $mimeType));
		$processedUploadedFile = $serializableFile->getAsFile();

		$this->assertEquals(realpath(sys_get_temp_dir()), realpath($processedUploadedFile->getPath()));
		$this->assertEquals($originalName, $processedUploadedFile->getClientOriginalName());
		$this->assertEquals($mimeType, $processedUploadedFile->getClientMimeType());
		$this->assertEquals('text/plain', $processedUploadedFile->getMimeType());
		$this->assertEquals(strlen(file_get_contents($document)), $processedUploadedFile->getSize());
	}

	public function testSerialization_minimalData() {
		$document = __DIR__ . self::DOCUMENT;
		$originalName = basename($document);

		$serializableFile = new SerializableFile($this->getNewUploadedFile($document, $originalName));
		$processedUploadedFile = $serializableFile->getAsFile();

		$this->assertEquals($originalName, $processedUploadedFile->getClientOriginalName());
		$this->assertEquals('application/octet-stream', $processedUploadedFile->getClientMimeType());
		$this->assertEquals('text/plain', $processedUploadedFile->getMimeType());
		$this->assertEquals(strlen(file_get_contents($document)), $processedUploadedFile->getSize());
	}

	public function testSerialization_customTempDir() {
		$this->tempFolder = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'craue_form_flow';
		if (!is_dir($this->tempFolder)) {
			mkdir($this->tempFolder);
		}

		$serializableFile = new SerializableFile($this->getNewUploadedFile(__FILE__, 'my.txt'));
		$processedUploadedFile = $serializableFile->getAsFile($this->tempFolder);

		$this->assertEquals(realpath($this->tempFolder), realpath($processedUploadedFile->getPath()));
	}

	public function testSerialization_customTempDir_nonexistent() {
		$serializableFile = new SerializableFile($this->getNewUploadedFile(__FILE__, 'my.txt'));
		$processedUploadedFile = @$serializableFile->getAsFile('xyz:/');

		$this->assertEquals(realpath(sys_get_temp_dir()), realpath($processedUploadedFile->getPath()));
	}

	/**
	 * @expectedException \Craue\FormFlowBundle\Exception\InvalidTypeException
	 * @expectedExceptionMessage Expected argument of type "Symfony\Component\HttpFoundation\File\UploadedFile", but "Symfony\Component\HttpFoundation\File\File" given.
	 */
	public function testSerialization_unsupportedType() {
		new SerializableFile(new File(__FILE__));
	}

	public function testIsSupported() {
		$this->assertTrue(SerializableFile::isSupported($this->getNewUploadedFile(__FILE__, basename(__FILE__))));
		$this->assertFalse(SerializableFile::isSupported(new File(__FILE__)));
	}

	/**
	 * @param string $document
	 * @param string $originalName
	 * @param string|null $mimeType
	 * @return UploadedFile
	 */
	private function getNewUploadedFile($document, $originalName, $mimeType = null) {
		// avoid a deprecation notice regarding "passing a size as 4th argument to the constructor"
		// TODO remove as soon as Symfony >= 4.1 is required
		if (property_exists(UploadedFile::class, 'size')) {
			return new UploadedFile($document, $originalName, $mimeType, null, null, true);
		}

		return new UploadedFile($document, $originalName, $mimeType, null, true);
	}

}
