<?php

namespace Craue\FormFlowBundle\Storage;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Stores data in the session.
 *
 * @author Toni Uebernickel <tuebernickel@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class SessionStorage implements StorageInterface {

	/**
	 * @var SessionInterface
	 */
	protected $session;

	public function __construct(SessionInterface $session) {
		$this->session = $session;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set($key, $value) {
		$this->session->set($key, $value);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get($key, $default = null) {
		return $this->session->get($key, $default);
	}

	/**
	 * {@inheritDoc}
	 */
	public function has($key) {
		return $this->session->has($key);
	}

	/**
	 * {@inheritDoc}
	 */
	public function remove($key) {
		return $this->session->remove($key);
	}

}
