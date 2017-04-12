<?php

namespace Craue\FormFlowBundle\Tests\Storage;

use Craue\FormFlowBundle\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class AbstractStorageTest extends TestCase {

	/**
	 * @var StorageInterface
	 */
	protected $storage;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp() {
		$this->storage = $this->getStorageImplementation();
	}

	/**
	 * @return StorageInterface
	 */
	abstract protected function getStorageImplementation();

	public function testSetGet() {
		$this->storage->set('foo', 'bar');
		$this->assertSame('bar', $this->storage->get('foo'));
	}

	public function testSetGet_overwrite() {
		$this->storage->set('foo', 'bar');
		$this->storage->set('foo', 'blah');
		$this->assertSame('blah', $this->storage->get('foo'));
	}

	public function testGet_empty() {
		$this->assertNull($this->storage->get('foo'));
	}

	public function testGet_default() {
		$this->assertSame('bar', $this->storage->get('foo', 'bar'));
	}

	public function testGet_defaultWithOtherKeyPresent() {
		$this->storage->set('foo1', 'bar1');
		$this->assertSame('bar2', $this->storage->get('foo2', 'bar2'));
	}

	public function testHas() {
		$this->storage->set('foo', 'bar');
		$this->assertTrue($this->storage->has('foo'));
	}

	public function testHas_empty() {
		$this->assertFalse($this->storage->has('foo'));
	}

	public function testRemove() {
		$this->storage->set('foo', 'bar');
		$this->storage->remove('foo');
		$this->assertFalse($this->storage->has('foo'));
	}

	public function testRemove_empty() {
		$this->storage->remove('foo');
		$this->assertFalse($this->storage->has('foo'));
	}

}
