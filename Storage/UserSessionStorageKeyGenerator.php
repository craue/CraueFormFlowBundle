<?php

namespace Craue\FormFlowBundle\Storage;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Generates a key unique for each user.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class UserSessionStorageKeyGenerator implements StorageKeyGeneratorInterface {

	/**
	 * @var TokenStorageInterface
	 */
	private $tokenStorage;

	/**
	 * @var SessionInterface
	 */
	private $session;

	public function __construct(TokenStorageInterface $tokenStorage, SessionInterface $session) {
		$this->tokenStorage = $tokenStorage;
		$this->session = $session;
	}

	/**
	 * {@inheritDoc}
	 */
	public function generate($key) {
		if (!is_string($key)) {
			throw new InvalidTypeException($key, 'string');
		}

		if (empty($key)) {
			throw new \InvalidArgumentException('Argument must not be empty.');
		}

		$token = $this->tokenStorage->getToken();

		if ($token instanceof TokenInterface && !$token instanceof AnonymousToken) {
			$username = $token->getUsername();
			if (!empty($username)) {
				return sprintf('user_%s_%s', $username, $key);
			}
		}

		// fallback to session id
		if (!$this->session->isStarted()) {
			$this->session->start();
		}

		return sprintf('session_%s_%s', $this->session->getId(), $key);
	}

}
