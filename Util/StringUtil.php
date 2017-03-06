<?php

namespace Craue\FormFlowBundle\Util;

use Craue\FormFlowBundle\Exception\InvalidTypeException;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class StringUtil {

	private function __construct() {}

	/**
	 * @param int $length
	 * @return string
	 */
	public static function generateRandomString($length) {
		if (!is_int($length)) {
			throw new InvalidTypeException($length, 'int');
		}

		if ($length < 0) {
			throw new \InvalidArgumentException(sprintf('Length must be >= 0, "%s" given.', $length));
		}

		return substr(rtrim(strtr(base64_encode(random_bytes($length)), '+/', '-_'), '='), 0, $length);
	}

	/**
	 * @param string $input
	 * @param int $length
	 * @return bool
	 */
	public static function isRandomString($input, $length) {
		if (!is_string($input)) {
			throw new InvalidTypeException($input, 'string');
		}

		if (!is_int($length)) {
			throw new InvalidTypeException($length, 'int');
		}

		if ($length < 0) {
			throw new \InvalidArgumentException(sprintf('Length must be >= 0, "%s" given.', $length));
		}

		return preg_match(sprintf('/^[a-zA-Z0-9-_]{%u}$/', $length), $input) === 1;
	}

	/**
	 * @param string $fqcn FQCN
	 * @return string|null flow name or null if not a FQCN
	 */
	public static function fqcnToFlowName($fqcn) {
		if (preg_match('/([^\\\\]+?)(flow)?$/i', $fqcn, $matches)) {
			return lcfirst(preg_replace('/([A-Z]+)([A-Z][a-z])/', '\\1\\2', $matches[1]));
		}
	}

}
