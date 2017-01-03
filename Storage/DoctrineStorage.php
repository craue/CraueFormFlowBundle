<?php

namespace Craue\FormFlowBundle\Storage;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;

/**
 * Stores data in a Doctrine-managed database.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class DoctrineStorage implements StorageInterface {

	const TABLE = 'craue_form_flow_storage';
	const KEY_COLUMN = 'key';
	const VALUE_COLUMN = 'value';

	/**
	 * @var Connection
	 */
	private $conn;

	/**
	 * @var StorageKeyGeneratorInterface
	 */
	private $storageKeyGenerator;

	/**
	 * @var AbstractSchemaManager
	 */
	private $schemaManager;

	/**
	 * @var string
	 */
	private $keyColumn;

	/**
	 * @var string
	 */
	private $valueColumn;

	public function __construct(Connection $conn, StorageKeyGeneratorInterface $storageKeyGenerator) {
		$this->conn = $conn;
		$this->storageKeyGenerator = $storageKeyGenerator;
		$this->schemaManager = $this->conn->getSchemaManager();
		$this->keyColumn = $this->conn->quoteIdentifier(self::KEY_COLUMN);
		$this->valueColumn = $this->conn->quoteIdentifier(self::VALUE_COLUMN);
	}

	/**
	 * {@inheritDoc}
	 */
	public function set($key, $value) {
		if (!$this->tableExists()) {
			$this->createTable();
		}

		if ($this->has($key)) {
			$this->conn->update(self::TABLE, array(
				$this->valueColumn => serialize($value),
			), array(
				$this->keyColumn => $this->generateKey($key),
			));

			return;
		}

		$this->conn->insert(self::TABLE, array(
			$this->keyColumn => $this->generateKey($key),
			$this->valueColumn => serialize($value),
		));
	}

	/**
	 * {@inheritDoc}
	 */
	public function get($key, $default = null) {
		if (!$this->tableExists()) {
			return $default;
		}

		$rawValue = $this->getRawValueForKey($key);

		if ($rawValue === false) {
			return $default;
		}

		return unserialize($rawValue);
	}

	/**
	 * {@inheritDoc}
	 */
	public function has($key) {
		if (!$this->tableExists()) {
			return false;
		}

		return $this->getRawValueForKey($key) !== false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function remove($key) {
		if (!$this->tableExists()) {
			return;
		}

		$this->conn->delete(self::TABLE, array(
			$this->keyColumn => $this->generateKey($key),
		));
	}

	/**
	 * Gets stored raw data for the given key.
	 * @param string $key
	 * @return string|false Raw data or false, if no data is available.
	 */
	private function getRawValueForKey($key) {
		$qb = $this->conn->createQueryBuilder()
			->select($this->valueColumn)
			->from(self::TABLE, 't') // alias needed only for DBAL < 2.5
			->where($this->keyColumn . ' = :key')
			->setParameter('key', $this->generateKey($key))
		;

		return $qb->execute()->fetchColumn();
	}

	private function tableExists() {
		return $this->schemaManager->tablesExist(self::TABLE);
	}

	private function createTable() {
		$table = new Table(self::TABLE, array(
			new Column($this->keyColumn, Type::getType(Type::STRING)),
			new Column($this->valueColumn, Type::getType(Type::TARRAY)),
		));
		$table->setPrimaryKey(array($this->keyColumn));
		$this->schemaManager->createTable($table);
	}

	private function generateKey($key) {
		return $this->storageKeyGenerator->generate($key);
	}

}
