<?php

namespace Craue\FormFlowBundle\Storage;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
interface StorageKeyGeneratorInterface {

	/**
	 * Generates a complete storage key based on the key the storage received.
	 * Usually, the given key would be appended to a user-unique identifier to achieve a session-like behavior.
	 * @param string $key
	 * @return string
	 */
	function generate($key);

}
