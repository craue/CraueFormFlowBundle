<?php

namespace Craue\FormFlowBundle\Storage;

/**
 * @author Toni Uebernickel <tuebernickel@gmail.com>
 * @copyright 2011-2012 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
interface StorageInterface
{
	/**
	 * Store the given value under the provided key.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	function set($key, $value);

	/**
	 * Retrieve the data stored under the given key.
	 *
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	function get($key, $default = null);

	/**
	 * Delete the stored data of the given key.
	 *
	 * @param string $key
	 */
	function remove($key);
}
