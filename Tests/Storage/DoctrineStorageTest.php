<?php

namespace Craue\FormFlowBundle\Tests\Storage;

use Craue\FormFlowBundle\Storage\DoctrineStorage;
use Craue\FormFlowBundle\Storage\StorageKeyGeneratorInterface;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2023 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class DoctrineStorageTest extends AbstractStorageTest {

	/**
	 * {@inheritDoc}
	 */
	protected function getStorageImplementation() {
		// TODO remove $configuration variable as soon as DBAL >= 4 is required
		$configuration = new Configuration();

		if (\method_exists($configuration, 'setSchemaManagerFactory')) {
			$configuration->setSchemaManagerFactory(new DefaultSchemaManagerFactory());
		}

		$conn = DriverManager::getConnection([
			'driver' => 'pdo_sqlite',
			'memory' => true,
		], $configuration);

		$generator = $this->createMock(StorageKeyGeneratorInterface::class);

		$generator
			->method('generate')
			->will($this->returnArgument(0))
		;

		return new DoctrineStorage($conn, $generator);
	}

	/**
	 * Ensure that quoted data is properly handled by DBAL.
	 * @dataProvider dataSetGet_stringsContainQuotes
	 */
	public function testSetGet_stringsContainQuotes($key, $value) {
		$this->storage->set($key, $value);
		$this->assertSame($value, $this->storage->get($key));
	}

	public function dataSetGet_stringsContainQuotes() {
		return [
			["f'oo", "b'ar"],
			['f"oo', 'b"ar'],
		];
	}

}
