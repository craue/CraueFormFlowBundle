<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\DependencyInjection;

use Craue\FormFlowBundle\Storage\DoctrineStorage;
use Craue\FormFlowBundle\Storage\SessionStorage;
use Craue\FormFlowBundle\Tests\IntegrationTestCase;

/**
 * @group integration
 * @group run-without-database
 * @group run-with-multiple-databases
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2025 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class StorageImplementationTest extends IntegrationTestCase {

	/**
	 * Ensure that, depending on the configuration, the correct {@link StorageInterface} implementation is used.
	 * This is useful because a compiler pass ({@link DoctrineStorageCompilerPass}) is needed to properly set up the
	 * database connection and the storage service while relying on an DI extension to load the (overridden) service
	 * may silently fail leading to a wrong implementation being used in tests.
	 */
	public function testUseCorrectStorageImplementation() {
		$storage = $this->getService('craue.form.flow.storage');
		$expectedClass = empty($_ENV['DB_DSN']) ? SessionStorage::class : DoctrineStorage::class;
		$this->assertInstanceOf($expectedClass, $storage);
	}

}
