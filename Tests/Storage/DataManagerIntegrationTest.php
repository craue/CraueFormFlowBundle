<?php

namespace Craue\FormFlowBundle\Tests\Storage;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Storage\DataManager;
use Craue\FormFlowBundle\Storage\SerializableFile;
use Craue\FormFlowBundle\Tests\IntegrationTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @group integration
 * @group run-with-multiple-databases-only
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2025 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class DataManagerIntegrationTest extends IntegrationTestCase {

	/**
	 * Ensure that a file uploaded within a flow is saved and restored correctly.
	 */
	public function testSaveLoad_file() : void {
		if (\version_compare(\PHP_VERSION, '7.4', '<') && ($_ENV['DB_FLAVOR'] ?? '') === 'postgresql') {
			$this->markTestSkipped('Would fail because SerializableFile::__serialize is only supported as of PHP 7.4.');
		}

		$session = new Session(new MockArraySessionStorage());
		$session->setId('12345');

		$request = Request::create('/');
		$request->setSession($session);

		$this->getService('request_stack')->push($request);

		/** @var $dataManager DataManager */
		$dataManager = $this->getService('craue.form.flow.data_manager');
		$dataManager->dropAll();

		$flow = $this->getFlow('testFlow', 'instance');

		$imagePath = __DIR__ . '/../Fixtures/blue-pixel.png';
		$imageFile = new UploadedFile($imagePath, basename($imagePath), 'image/png', null, true);

		$dataIn = ['photo' => new SerializableFile($imageFile)];
		$dataOut = ['photo' => $imageFile];

		$dataManager->save($flow, $dataIn);

		$this->assertEquals($dataOut, $dataManager->load($flow));
	}

	/**
	 * @return MockObject|FormFlow
	 */
	private function getFlow(string $name, string $instanceId) {
		$flow = $this->getMockBuilder(FormFlow::class)->onlyMethods(['getName', 'isHandleFileUploads'])->getMock();

		$flow
			->method('getName')
			->will($this->returnValue($name))
		;

		$flow
			->method('isHandleFileUploads')
			->will($this->returnValue(true))
		;

		$flow->setInstanceId($instanceId);

		return $flow;
	}

}
