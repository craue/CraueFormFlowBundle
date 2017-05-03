<?php

namespace Craue\FormFlowBundle\Storage;

use Craue\FormFlowBundle\Form\FormFlowInterface;

/**
 * Manages data of flows and their steps.
 *
 * It uses the following data structure with {@link DataManagerInterface::STORAGE_ROOT} as name of the root element within the storage:
 * <code>
 * 	DataManagerInterface::STORAGE_ROOT => array(
 * 		name of the flow => array(
 * 			instance id of the flow => array(
 * 				'data' => array() // the actual step data
 * 			)
 * 		)
 * 	)
 * </code>
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class DataManager implements ExtendedDataManagerInterface {

	/**
	 * @var string Key for the actual step data.
	 */
	const DATA_KEY = 'data';

	/**
	 * @var StorageInterface
	 */
	private $storage;

	/**
	 * @param StorageInterface $storage
	 */
	public function __construct(StorageInterface $storage) {
		$this->storage = $storage;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStorage() {
		return $this->storage;
	}

	/**
	 * {@inheritDoc}
	 */
	public function save(FormFlowInterface $flow, array $data) {
		// handle file uploads
		if ($flow->isHandleFileUploads()) {
			array_walk_recursive($data, function(&$value, $key) {
				if (SerializableFile::isSupported($value)) {
					$value = new SerializableFile($value);
				}
			});
		}

		// drop old data
		$this->drop($flow);

		// save new data
		$savedFlows = $this->storage->get(DataManagerInterface::STORAGE_ROOT, array());

		$savedFlows = array_merge_recursive($savedFlows, array(
			$flow->getName() => array(
				$flow->getInstanceId() => array(
					self::DATA_KEY => $data,
				),
			),
		));

		$this->storage->set(DataManagerInterface::STORAGE_ROOT, $savedFlows);
	}

	/**
	 * {@inheritDoc}
	 */
	public function load(FormFlowInterface $flow) {
		$data = array();

		// try to find data for the given flow
		$savedFlows = $this->storage->get(DataManagerInterface::STORAGE_ROOT, array());
		if (isset($savedFlows[$flow->getName()][$flow->getInstanceId()][self::DATA_KEY])) {
			$data = $savedFlows[$flow->getName()][$flow->getInstanceId()][self::DATA_KEY];
		}

		// handle file uploads
		if ($flow->isHandleFileUploads()) {
			$tempDir = $flow->getHandleFileUploadsTempDir();
			array_walk_recursive($data, function(&$value, $key) use ($tempDir) {
				if ($value instanceof SerializableFile) {
					$value = $value->getAsFile($tempDir);
				}
			});
		}

		return $data;
	}

	/**
	 * {@inheritDoc}
	 */
	public function exists(FormFlowInterface $flow) {
		$savedFlows = $this->storage->get(DataManagerInterface::STORAGE_ROOT, array());
		return isset($savedFlows[$flow->getName()][$flow->getInstanceId()][self::DATA_KEY]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function drop(FormFlowInterface $flow) {
		$savedFlows = $this->storage->get(DataManagerInterface::STORAGE_ROOT, array());

		// remove data for only this flow instance
		unset($savedFlows[$flow->getName()][$flow->getInstanceId()]);

		$this->storage->set(DataManagerInterface::STORAGE_ROOT, $savedFlows);
	}

	/**
	 * {@inheritDoc}
	 */
	public function listFlows() {
		return array_keys($this->storage->get(DataManagerInterface::STORAGE_ROOT, array()));
	}

	/**
	 * {@inheritDoc}
	 */
	public function listInstances($name) {
		$savedFlows = $this->storage->get(DataManagerInterface::STORAGE_ROOT, array());

		if (array_key_exists($name, $savedFlows)) {
			return array_keys($savedFlows[$name]);
		}

		return array();
	}

	/**
	 * {@inheritDoc}
	 */
	public function dropAll() {
		$this->storage->remove(DataManagerInterface::STORAGE_ROOT);
	}

}
