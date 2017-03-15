<?php

namespace Craue\FormFlowBundle\Tests\Storage;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Storage\DataManager;
use Craue\FormFlowBundle\Storage\DataManagerInterface;
use Craue\FormFlowBundle\Storage\ExtendedDataManagerInterface;
use Craue\FormFlowBundle\Storage\SessionStorage;
use Craue\FormFlowBundle\Storage\StorageInterface;
use Craue\FormFlowBundle\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class DataManagerTest extends UnitTestCase {

	/**
	 * @var StorageInterface
	 */
	protected $storage;

	/**
	 * @var ExtendedDataManagerInterface
	 */
	protected $dataManager;

	protected $createLocationFlowName = 'createLocation';
	protected $createLocationFlowInstanceId = '26xz98wx38';
	protected $createLocationFlowData = array(
		// step 1
		array(
			'country' => 'US',
			'_token' => '81jaZU6lQD_oCV-BmTZSxAN3rORRQgAVEu4iZbsACRE',
		),
		// step 2
		array(
			'region' => 'US-TX',
			'_token' => 'ytCzDJAGrpo7ToVKqU89IOIQ0sDW2LDeve_5X0x6Sy0',
		),
	);

	protected $createVehicleFlowName = 'createVehicle';
	protected $createVehicleFlowInstanceId = '3a03y1o9at';
	protected $createVehicleFlowData = array(
		// step 1
		array(
			'vehicle' => array(
				'numberOfWheels' => '2',
			),
			'_token' => 'sGcKUDmMmeaLedFQhoDt2ULi_39hErh4ZqZN1qZDCjc',
		),
	);

	/**
	 * {@inheritDoc}
	 */
	protected function setUp() {
		$this->storage = new SessionStorage(new Session(new MockArraySessionStorage()));
		$this->dataManager = new DataManager($this->storage);
	}

	public function testGetStorage() {
		$this->assertSame($this->storage, $this->dataManager->getStorage());
	}

	public function testSaveLoadDropExists() {
		// create two flows
		$createLocationFlow = $this->getFlow($this->createLocationFlowName, $this->createLocationFlowInstanceId);
		$createVehicleFlow = $this->getFlow($this->createVehicleFlowName, $this->createVehicleFlowInstanceId);

		// ensure there's no stored data for new flows
		$this->assertFalse($this->dataManager->exists($createLocationFlow));
		$this->assertFalse($this->dataManager->exists($createVehicleFlow));

		// save their data
		$this->dataManager->save($createLocationFlow, $this->createLocationFlowData);
		$this->dataManager->save($createVehicleFlow, $this->createVehicleFlowData);

		// ensure their data exists in the storage
		$this->assertTrue($this->dataManager->exists($createLocationFlow));
		$this->assertTrue($this->dataManager->exists($createVehicleFlow));

		// ensure their data has been saved correctly
		$this->assertEquals($this->createLocationFlowData, $this->dataManager->load($createLocationFlow));
		$this->assertEquals($this->createVehicleFlowData, $this->dataManager->load($createVehicleFlow));

		// drop data for one of them
		$this->dataManager->drop($createLocationFlow);

		// ensure only data of the correct flow exists in the storage
		$this->assertFalse($this->dataManager->exists($createLocationFlow));
		$this->assertTrue($this->dataManager->exists($createVehicleFlow));

		// ensure only data of the correct flow has been dropped
		$this->assertEquals(array(), $this->dataManager->load($createLocationFlow));
		$this->assertEquals($this->createVehicleFlowData, $this->dataManager->load($createVehicleFlow));
	}

	public function testSave_overwriteOldDate() {
		// create a flow
		$createLocationFlow = $this->getFlow($this->createLocationFlowName, $this->createLocationFlowInstanceId);

		// save its data
		$this->dataManager->save($createLocationFlow, $this->createLocationFlowData);

		$newData = array('blah' => 'blah');

		// save changed data
		$this->dataManager->save($createLocationFlow, $newData);

		// ensure its data has been overwritten correctly
		$this->assertEquals($newData, $this->dataManager->load($createLocationFlow));
	}

	/**
	 * Ensure that even without any data an array is returned.
	 */
	public function testLoad_emptyStorage() {
		$createLocationFlow = $this->getFlow($this->createLocationFlowName, $this->createLocationFlowInstanceId);
		$this->assertEquals(array(), $this->dataManager->load($createLocationFlow));
	}

	public function testListFlows() {
		// create three flows
		$createLocationFlow1 = $this->getFlow($this->createLocationFlowName, $this->createLocationFlowInstanceId);
		$createLocationFlow2 = $this->getFlow($this->createLocationFlowName, 'other-instance');
		$createVehicleFlow = $this->getFlow($this->createVehicleFlowName, $this->createVehicleFlowInstanceId);

		// save their data
		$this->dataManager->save($createLocationFlow1, $this->createLocationFlowData);
		$this->dataManager->save($createLocationFlow2, $this->createLocationFlowData);
		$this->dataManager->save($createVehicleFlow, $this->createVehicleFlowData);

		// get names of all flows
		$expectedResult = array(
			$this->createLocationFlowName,
			$this->createVehicleFlowName,
		);
		$this->assertEquals($expectedResult, $this->dataManager->listFlows());
	}

	/**
	 * Ensure that even without any data an array is returned.
	 */
	public function testListFlows_emptyStorage() {
		$this->assertEquals(array(), $this->dataManager->listFlows());
	}

	public function testListInstances() {
		// create three flows
		$createLocationFlow1 = $this->getFlow($this->createLocationFlowName, $this->createLocationFlowInstanceId);
		$createLocationFlow2 = $this->getFlow($this->createLocationFlowName, 'other-instance');
		$createVehicleFlow = $this->getFlow($this->createVehicleFlowName, $this->createVehicleFlowInstanceId);

		// save their data
		$this->dataManager->save($createLocationFlow1, $this->createLocationFlowData);
		$this->dataManager->save($createLocationFlow2, $this->createLocationFlowData);
		$this->dataManager->save($createVehicleFlow, $this->createVehicleFlowData);

		// get instances of one flow
		$expectedResult = array(
			$this->createLocationFlowInstanceId,
			'other-instance',
		);
		$this->assertEquals($expectedResult, $this->dataManager->listInstances($this->createLocationFlowName));
	}

	/**
	 * Ensure that even without any data an array is returned.
	 */
	public function testListInstances_emptyStorage() {
		$this->assertEquals(array(), $this->dataManager->listInstances('whatever'));
	}

	public function testDropAll() {
		// create a flow
		$createLocationFlow = $this->getFlow($this->createLocationFlowName, $this->createLocationFlowInstanceId);

		// save its data
		$this->dataManager->save($createLocationFlow, $this->createLocationFlowData);

		// drop all data
		$this->dataManager->dropAll();

		// ensure all data has been dropped
		$this->assertFalse($this->storage->has(DataManagerInterface::STORAGE_ROOT));
	}

	/**
	 * @param string $name
	 * @param string $instanceId
	 * @return \PHPUnit_Framework_MockObject_MockObject|FormFlow
	 */
	protected function getFlow($name, $instanceId) {
		$flow = $this->getFlowWithMockedMethods(array('getName'));

		$flow
			->method('getName')
			->will($this->returnValue($name))
		;

		$flow->setInstanceId($instanceId);

		return $flow;
	}

}
