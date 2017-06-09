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
 * @copyright 2011-2017 Christian Raue
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
		$size = strlen(file_get_contents($document));

		$serializableFile = new SerializableFile(new UploadedFile($document, $originalName, $mimeType, $size, null, true));
		$processedUploadedFile = $serializableFile->getAsFile();

		$this->assertEquals(realpath(sys_get_temp_dir()), realpath($processedUploadedFile->getPath()));
		$this->assertEquals($originalName, $processedUploadedFile->getClientOriginalName());
		$this->assertEquals($mimeType, $processedUploadedFile->getClientMimeType());
		$this->assertEquals('text/plain', $processedUploadedFile->getMimeType());
		$this->assertEquals($size, $processedUploadedFile->getClientSize());
		$this->assertEquals($size, $processedUploadedFile->getSize());
	}

	public function testSerialization_minimalData() {
		$document = __DIR__ . self::DOCUMENT;
		$originalName = basename($document);

		$serializableFile = new SerializableFile(new UploadedFile($document, $originalName, null, null, null, true));
		$processedUploadedFile = $serializableFile->getAsFile();

		$this->assertEquals($originalName, $processedUploadedFile->getClientOriginalName());
		$this->assertEquals('application/octet-stream', $processedUploadedFile->getClientMimeType());
		$this->assertEquals('text/plain', $processedUploadedFile->getMimeType());
		$this->assertEquals(0, $processedUploadedFile->getClientSize());
		$this->assertEquals(strlen(file_get_contents($document)), $processedUploadedFile->getSize());
	}

	public function testSerialization_customTempDir() {
		$this->tempFolder = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'craue_form_flow';
		if (!is_dir($this->tempFolder)) {
			mkdir($this->tempFolder);
		}

		$serializableFile = new SerializableFile(new UploadedFile(__FILE__, 'my.txt', null, null, null, true));
		$processedUploadedFile = $serializableFile->getAsFile($this->tempFolder);

		$this->assertEquals(realpath($this->tempFolder), realpath($processedUploadedFile->getPath()));
	}

	public function testSerialization_customTempDir_nonexistent() {
		$serializableFile = new SerializableFile(new UploadedFile(__FILE__, 'my.txt', null, null, null, true));
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
		$this->assertTrue(SerializableFile::isSupported(new UploadedFile(__FILE__, basename(__FILE__))));
		$this->assertFalse(SerializableFile::isSupported(new File(__FILE__)));
	}

}
