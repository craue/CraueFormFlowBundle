<?php

namespace App\Form\Flow;

use Craue\FormFlowBundle\Form\FormFlowInterface;
use Craue\FormFlowBundle\Storage\DataManagerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;

/**
 * Manages data of flows and their steps.
 *
 * It uses the following data structure with {@link DataManagerInterface::STORAGE_ROOT} as name of the root element within the storage:
 * <code>
 *    DataManagerInterface::STORAGE_ROOT => [
 *        name of the flow => [
 *            instance id of the flow => [
 *                'data' => [] // the actual step data
 *            ]
 *        ]
 *    ]
 * </code>
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class DoctrineManager implements DataManagerInterface
{
    const TABLE = 'craue_form_flow_storage';
    const KEY_COLUMN = 'key';
    const VALUE_COLUMN = 'value';

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $keyColumn;

    /**
     * @var string
     */
    private $valueColumn;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var \Doctrine\DBAL\Schema\AbstractSchemaManager|null
     */
    private $schemaManager;

    public function __construct(
        Connection $connection,
        $table = self::TABLE,
        $keyColumn = self::KEY_COLUMN,
        $valueColumn = self::VALUE_COLUMN
    ) {
        $this->connection = $connection;
        $this->schemaManager = $connection->getSchemaManager();
        $this->table = $table;
        $this->keyColumn = $connection->quoteIdentifier($keyColumn);
        $this->valueColumn = $connection->quoteIdentifier($valueColumn);
    }

    /**
     * {@inheritDoc}
     */
    public function getStorage()
    {
        return;
    }

    /**
     * {@inheritDoc}
     */
    public function save(FormFlowInterface $flow, array $data)
    {
        if (!$this->tableExists()) {
            $this->createTable();
        }

        $instanceId = $flow->getInstanceId();

        if ($this->has($instanceId)) {
            $this->connection->update(
                $this->table,
                [
                    $this->valueColumn => serialize($data),
                ],
                [
                    $this->keyColumn => $instanceId,
                ]
            );

            return;
        }

        $this->connection->insert(
            $this->table,
            [
                $this->keyColumn => $instanceId,
                $this->valueColumn => serialize($data),
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function load(FormFlowInterface $flow)
    {
        if (!$this->tableExists()) {
            return [];
        }
        $key = $flow->getInstanceId();

        $rawValue = $this->getRawValueForKey($key);

        if ($rawValue === false) {
            return [];
        }

        return unserialize($rawValue);
    }

    /**
     * {@inheritDoc}
     */
    public function exists(FormFlowInterface $flow)
    {
        if (!$this->tableExists()) {
            return false;
        }
        $key = $flow->getInstanceId();

        return $this->getRawValueForKey($key) !== false;
    }

    /**
     * {@inheritDoc}
     */
    public function drop(FormFlowInterface $flow)
    {
        if (!$this->tableExists()) {
            return;
        }
        $key = $flow->getInstanceId();

        $this->connection->delete(
            $this->table,
            [
                $this->keyColumn => $key,
            ]
        );
    }

    private function has($key)
    {
        if (!$this->tableExists()) {
            return false;
        }

        return $this->getRawValueForKey($key) !== false;
    }

    private function getRawValueForKey($key)
    {
        $qb = $this->connection->createQueryBuilder()
            ->select($this->valueColumn)
            ->from($this->table)
            ->where($this->keyColumn.' = :key')
            ->setParameter('key', $key);

        return $qb->execute()->fetchColumn();
    }

    private function tableExists()
    {
        return $this->schemaManager->tablesExist([$this->table]);
    }

    private function createTable()
    {
        $table = new Table(
            $this->table, [
                new Column($this->keyColumn, Type::getType(Types::STRING)),
                new Column($this->valueColumn, Type::getType(Types::ARRAY)),
            ]
        );
        $table->setPrimaryKey([$this->keyColumn]);
        $this->schemaManager->createTable($table);
    }
}
