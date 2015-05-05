<?php

namespace Craue\FormFlowBundle\Util;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class DataTypeUtil {

	private function __construct() {}

	/**
	 * @param mixed $value
	 * @return boolean If the given value is an array and either contains only strings or is empty.
	 */
	public static function isStringArray($value) {
		if (is_array($value)) {
			foreach ($value as $entry) {
				if (!is_string($entry)) {
					return false;
				}
			}

			return true;
		}

		return false;
	}

}
