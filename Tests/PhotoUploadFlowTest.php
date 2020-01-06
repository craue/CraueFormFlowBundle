<?php

namespace Craue\FormFlowBundle\Tests;

use Craue\FormFlowBundle\Util\TempFileUtil;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PhotoUploadFlowTest extends IntegrationTestCase {

	const IMAGE = '/Fixtures/blue-pixel.png';

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

}
