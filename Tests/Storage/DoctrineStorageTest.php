<?php

namespace Craue\FormFlowBundle\Tests\Storage;

use Craue\FormFlowBundle\Storage\DoctrineStorage;
use Craue\FormFlowBundle\Storage\StorageKeyGeneratorInterface;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;

/**
 * @group unit
 * @group run-with-multiple-databases
 * @group run-with-multiple-databases-only
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2023 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class DoctrineStorageTest extends AbstractStorageTest {

	/**
	 * @var Connection
	 */
	private $conn;

	/**
	 * {@inheritDoc}
	 */
	protected function getStorageImplementation() {
		// TODO remove $configuration variable as soon as DBAL >= 4 is required
		$configuration = new Configuration();

		if (\method_exists($configuration, 'setSchemaManagerFactory')) {
			$configuration->setSchemaManagerFactory(new DefaultSchemaManagerFactory());
		}

		if (empty($_ENV['DB_DSN'])) {
			$this->markTestSkipped('Environment variable DB_DSN is not set.');
		}

		$this->conn = DriverManager::getConnection([
			'url' => $_ENV['DB_DSN'],
		], $configuration);

		$generator = $this->createMock(StorageKeyGeneratorInterface::class);

		$generator
			->method('generate')
			->will($this->returnArgument(0))
		;

		return new DoctrineStorage($this->conn, $generator);
	}

	protected function setUp() : void {
		parent::setUp();

		// TODO just call `createSchemaManager()` as soon as DBAL >= 3.1 is required
		$schemaManager = \method_exists($this->conn, 'createSchemaManager') ? $this->conn->createSchemaManager() : $this->conn->getSchemaManager();

		// ensure the table doesn't exist
		if ($schemaManager->tablesExist([DoctrineStorage::TABLE])) {
			$schemaManager->dropTable(DoctrineStorage::TABLE);
		}
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
