<?php

namespace Craue\FormFlowBundle\Storage;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @internal
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2024 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
trait SessionProviderTrait {

	/**
	 * @var RequestStack|null
	 */
	private $requestStack;

	/**
	 * @var SessionInterface|null
	 */
	private $session;

	/**
	 * @param RequestStack|SessionInterface $requestStackOrSession
	 * @throws InvalidTypeException
	 */
	private function setRequestStackOrSession($requestStackOrSession) : void {
		// TODO accept only RequestStack as soon as Symfony >= 6.0 is required

		if ($requestStackOrSession instanceof SessionInterface) {
			$this->session = $requestStackOrSession;

			return;
		}

		if ($requestStackOrSession instanceof RequestStack) {
			$this->requestStack = $requestStackOrSession;

			return;
		}

		throw new InvalidTypeException($requestStackOrSession, [RequestStack::class, SessionInterface::class]);
	}

	private function getSession() : SessionInterface {
		if ($this->requestStack !== null) {
			return $this->requestStack->getSession();
		}

		return $this->session;
	}

}
