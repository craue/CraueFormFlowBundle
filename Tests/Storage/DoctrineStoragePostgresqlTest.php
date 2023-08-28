<?php

namespace Craue\FormFlowBundle\Tests\Storage;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2023 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class DoctrineStoragePostgresqlTest extends AbstractDoctrineStorageTest {

	protected function getConnectionDsnEnvVar() : string {
		return 'TEST_DB_FLAVOR_POSTGRESQL_DSN';
	}

}
