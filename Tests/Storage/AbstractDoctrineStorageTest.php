<?php

namespace Craue\FormFlowBundle\Tests\Storage;

use Craue\FormFlowBundle\Storage\DoctrineStorage;
use Craue\FormFlowBundle\Storage\StorageInterface;
use Craue\FormFlowBundle\Storage\StorageKeyGeneratorInterface;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2023 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class AbstractDoctrineStorageTest extends AbstractStorageTest {

	/**
	 * @var Connection
	 */
	private $conn;

	abstract protected function getConnectionDsnEnvVar() : string;

	protected function getConnection() : Connection {
		return $this->conn;
	}

	protected function getStorageImplementation() : StorageInterface {
		// TODO remove $configuration variable as soon as DBAL >= 4 is required
		$configuration = new Configuration();

		if (\method_exists($configuration, 'setSchemaManagerFactory')) {
			$configuration->setSchemaManagerFactory(new DefaultSchemaManagerFactory());
		}

		$dsnEnvVar = $this->getConnectionDsnEnvVar();

		if (empty($_ENV[$dsnEnvVar])) {
			$this->markTestSkipped(sprintf('Environment variable %s is not set.', $dsnEnvVar));
		}

		$this->conn = DriverManager::getConnection([
			'url' => $_ENV[$dsnEnvVar],
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
