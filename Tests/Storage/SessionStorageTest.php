<?php

namespace Craue\FormFlowBundle\Tests\Storage;

use Craue\FormFlowBundle\Storage\SessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class SessionStorageTest extends AbstractStorageTest {

	/**
	 * {@inheritDoc}
	 */
	protected function getStorageImplementation() {
		return new SessionStorage(new Session(new MockArraySessionStorage()));
	}

}
