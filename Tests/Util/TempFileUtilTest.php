<?php

namespace Craue\FormFlowBundle\Tests\Util;

use Craue\FormFlowBundle\Util\TempFileUtil;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class TempFileUtilTest extends TestCase {

	public function testAddAndRemoveFiles() {
		$tempFile = tempnam(sys_get_temp_dir(), 'craue_form_flow_temp_file');
		$this->assertFileExists($tempFile);

		TempFileUtil::addTempFile($tempFile);
		$this->assertCount(1, $this->getTempFiles());

		// add same file again to ensure that no warning is triggered while trying to remove a non-existing file
		TempFileUtil::addTempFile($tempFile);
		$this->assertCount(2, $this->getTempFiles());

		TempFileUtil::removeTempFiles();
		$this->assertCount(0, $this->getTempFiles());
		$this->assertFileNotExists($tempFile);
	}

	private function getTempFiles() {
		$reflectionClass = new \ReflectionClass(TempFileUtil::class);
		$staticProperties = $reflectionClass->getStaticProperties();

		return $staticProperties['tempFiles'];
	}

}
