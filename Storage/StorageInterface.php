<?php

namespace Craue\FormFlowBundle\Storage;

/**
 * @author Toni Uebernickel <tuebernickel@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
interface StorageInterface {

	/**
	 * Store the given value under the given key.
	 * @param string $key
	 * @param mixed $value
	 */
	function set($key, $value);

	/**
	 * Retrieve the data stored under the given key.
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	function get($key, $default = null);

	/**
	 * Checks if data is stored for the given key.
	 * @param string $key
	 * @return boolean
	 */
	function has($key);

	/**
	 * Delete the stored data of the given key.
	 * @param string $key
	 * @return mixed The removed value.
	 */
	function remove($key);

}
