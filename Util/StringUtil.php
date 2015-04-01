<?php

namespace Craue\FormFlowBundle\Util;

use Craue\FormFlowBundle\Exception\InvalidTypeException;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class StringUtil {

	private function __construct() {}

	/**
	 * @param integer $length
	 * @return string
	 */
	public static function generateRandomString($length) {
		if (!is_int($length)) {
			throw new InvalidTypeException($length, 'integer');
		}

		if ($length < 0) {
			throw new \InvalidArgumentException(sprintf('Length must be >= 0, "%s" given.', $length));
		}

		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789-';
		$maxCharsIndex = strlen($chars) - 1;

		$result = '';

		for ($i = $length; $i > 0; --$i) {
			$result .= $chars[mt_rand(0, $maxCharsIndex)];
		}

		return $result;
	}

	/**
	 * @param string $input
	 * @param integer $length
	 * @return boolean
	 */
	public static function isRandomString($input, $length) {
		if (!is_string($input)) {
			throw new InvalidTypeException($input, 'string');
		}

		if (!is_int($length)) {
			throw new InvalidTypeException($length, 'integer');
		}

		if ($length < 0) {
			throw new \InvalidArgumentException(sprintf('Length must be >= 0, "%s" given.', $length));
		}

		return preg_match(sprintf('/^[a-z0-9-]{%u}$/', $length), $input) === 1;
	}

}
