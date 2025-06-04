<?php

namespace Craue\FormFlowBundle\Storage;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Generates a key unique for each user.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2025 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class UserSessionStorageKeyGenerator implements StorageKeyGeneratorInterface {

	use SessionProviderTrait;

	/**
	 * @var TokenStorageInterface
	 */
	private $tokenStorage;

	/**
	 * @param TokenStorageInterface $tokenStorage
	 * @param RequestStack|SessionInterface $requestStackOrSession
	 * @throws InvalidTypeException
	 */
	public function __construct(TokenStorageInterface $tokenStorage, $requestStackOrSession) {
		$this->tokenStorage = $tokenStorage;
		$this->setRequestStackOrSession($requestStackOrSession);
	}

	/**
	 * {@inheritDoc}
	 */
	public function generate($key) {
		if (!is_string($key)) {
			throw new InvalidTypeException($key, 'string');
		}

		if ($key === '') {
			throw new \InvalidArgumentException('Argument must not be empty.');
		}

		$token = $this->tokenStorage->getToken();

		// TODO remove checks for AnonymousToken as soon as Symfony >= 6.0 is required
		if ($token instanceof TokenInterface && (!\class_exists(AnonymousToken::class) || !$token instanceof AnonymousToken)) {
			$userIdentifier = $token->getUserIdentifier();
			if ($userIdentifier !== '') {
				return sprintf('user_%s_%s', $userIdentifier, $key);
			}
		}

		// fallback to session id
		$session = $this->getSession();

		if (!$session->isStarted()) {
			$session->start();
		}

		return sprintf('session_%s_%s', $session->getId(), $key);
	}

}
