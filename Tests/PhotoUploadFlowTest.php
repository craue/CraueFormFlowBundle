<?php

namespace Craue\FormFlowBundle\Tests;

use Craue\FormFlowBundle\Util\TempFileUtil;

/**
 * @group integration
 * @group run-with-multiple-databases
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2024 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PhotoUploadFlowTest extends IntegrationTestCase {

	const IMAGE = '/Fixtures/blue-pixel.png';

	protected function setUp() : void {
		if (\version_compare(\PHP_VERSION, '7.4', '<') && ($_ENV['DB_FLAVOR'] ?? '') === 'postgresql') {
			$this->markTestSkipped('Would fail because SerializableFile::__serialize is only supported as of PHP 7.4.');
		}

		parent::setUp();
	}

	public function testPhotoUpload() {
		$image = __DIR__ . self::IMAGE;

		$crawler = static::$client->request('GET', $this->url('_FormFlow_photoUpload'));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"photo":null,"comment":null}', $crawler);

		// upload the photo
		$form = $crawler->selectButton('next')->form();
		$form['photoUpload[photo]']->upload($image);

		// allow the temporary file created by the DomCrawler to be removed after the test
		$fileFieldValue = $form['photoUpload[photo]']->getValue();
		TempFileUtil::addTempFile($fileFieldValue['tmp_name']);

		// submit the form -> step 2
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{"photo":{},"comment":null}', $crawler);

		// comment -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'photoUpload[comment]' => 'blah',
		]);
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertCurrentFormData('{"photo":{},"comment":"blah"}', $crawler);
		$this->assertRenderedImageUrl(sprintf('data:image/png;base64,%s', base64_encode(file_get_contents($image))), $crawler);
	}

	public function testPhotoInsideCollectionUpload() {
		$image = __DIR__ . self::IMAGE;

		$crawler = static::$client->request('GET', $this->url('_FormFlow_photoCollectionUpload'));
		$this->assertSame(200, static::$client->getResponse()->getStatusCode());
		$this->assertCurrentStepNumber(1, $crawler);
		$this->assertCurrentFormData('{"photos":{},"comment":null}', $crawler);

		// upload the photo
		$form = $crawler->selectButton('next')->form();
		$form['photoCollectionUpload[photos][0][photo]']->upload($image);
		$form['photoCollectionUpload[photos][0][comment]']->setValue('a beautiful image');

		// allow the temporary file created by the DomCrawler to be removed after the test
		$fileFieldValue = $form['photoCollectionUpload[photos][0][photo]']->getValue();
		TempFileUtil::addTempFile($fileFieldValue['tmp_name']);

		// submit the form -> step 2
		$crawler = static::$client->submit($form);
		$this->assertCurrentStepNumber(2, $crawler);
		$this->assertCurrentFormData('{"photos":{},"comment":null}', $crawler);

		// comment -> step 3
		$form = $crawler->selectButton('next')->form();
		$crawler = static::$client->submit($form, [
			'photoCollectionUpload[comment]' => 'blah',
		]);
		$this->assertCurrentStepNumber(3, $crawler);
		$this->assertCurrentFormData('{"photos":{},"comment":"blah"}', $crawler);
		$this->assertRenderedImageCollectionCount(1, $crawler);
	}

}
