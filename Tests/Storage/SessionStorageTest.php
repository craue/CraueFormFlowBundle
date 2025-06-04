<?php

namespace Craue\FormFlowBundle\Tests\Storage;

use Craue\FormFlowBundle\Storage\SessionStorage;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2025 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class SessionStorageTest extends AbstractStorageTest {

	/**
	 * {@inheritDoc}
	 */
	protected function getStorageImplementation() {
		$session = new Session(new MockArraySessionStorage());

		$requestStackMock = $this->getMockBuilder(RequestStack::class)->onlyMethods(['getSession'])->getMock();

		$requestStackMock
			->method('getSession')
			->willReturn($session)
		;

		return new SessionStorage($requestStackMock);
	}

}
