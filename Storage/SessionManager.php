<?php

namespace Craue\FormFlowBundle\Storage;

use Craue\FormFlowBundle\Form\FormFlowInterface;

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
class SessionManager implements DataManagerInterface
{
    /**
     * @var string Key for the actual step data.
     */
    const DATA_KEY = 'data';

    /**
     * @var SessionInterface
     */
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritDoc}
     */
    public function save(FormFlowInterface $flow, array $data)
    {
        // handle file uploads
        if ($flow->isHandleFileUploads()) {
            array_walk_recursive(
                $data,
                function (&$value, $key) {
                    if (SerializableFile::isSupported($value)) {
                        $value = new SerializableFile($value);
                    }
                }
            );
        }

        // drop old data
        $this->drop($flow);

        // save new data
        $savedFlows = $this->session->get(DataManagerInterface::STORAGE_ROOT, []);

        $savedFlows = array_merge_recursive(
            $savedFlows,
            [
                $flow->getName() => [
                    $flow->getInstanceId() => [
                        self::DATA_KEY => $data,
                    ],
                ],
            ]
        );

        $this->session->set(DataManagerInterface::STORAGE_ROOT, $savedFlows);
    }

    /**
     * {@inheritDoc}
     */
    public function load(FormFlowInterface $flow)
    {
        $data = [];

        // try to find data for the given flow
        $savedFlows = $this->session->get(DataManagerInterface::STORAGE_ROOT, []);
        if (isset($savedFlows[$flow->getName()][$flow->getInstanceId()][self::DATA_KEY])) {
            $data = $savedFlows[$flow->getName()][$flow->getInstanceId()][self::DATA_KEY];
        }

        // handle file uploads
        if ($flow->isHandleFileUploads()) {
            $tempDir = $flow->getHandleFileUploadsTempDir();
            array_walk_recursive(
                $data,
                function (&$value, $key) use ($tempDir) {
                    if ($value instanceof SerializableFile) {
                        $value = $value->getAsFile($tempDir);
                    }
                }
            );
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function exists(FormFlowInterface $flow)
    {
        $savedFlows = $this->session->get(DataManagerInterface::STORAGE_ROOT, []);

        return isset($savedFlows[$flow->getName()][$flow->getInstanceId()][self::DATA_KEY]);
    }

    /**
     * {@inheritDoc}
     */
    public function drop(FormFlowInterface $flow)
    {
        $savedFlows = $this->session->get(DataManagerInterface::STORAGE_ROOT, []);

        // remove data for only this flow instance
        unset($savedFlows[$flow->getName()][$flow->getInstanceId()]);

        $this->session->set(DataManagerInterface::STORAGE_ROOT, $savedFlows);
    }

    /**
     * {@inheritDoc}
     */
    public function listFlows()
    {
        return array_keys($this->session->get(DataManagerInterface::STORAGE_ROOT, []));
    }

    /**
     * {@inheritDoc}
     */
    public function listInstances($name)
    {
        $savedFlows = $this->session->get(DataManagerInterface::STORAGE_ROOT, []);

        if (array_key_exists($name, $savedFlows)) {
            return array_keys($savedFlows[$name]);
        }

        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function dropAll()
    {
        $this->session->remove(DataManagerInterface::STORAGE_ROOT);
    }

}
