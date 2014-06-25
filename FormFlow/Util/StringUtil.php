<?php

namespace Craue\FormFlowBundle\FormFlow\Util;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class StringUtil {

	private function __construct() {}

	/**
	 * @param integer $length
	 * @return string
	 */
	public static function generateRandomString($length) {
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789-';
		$maxCharsIndex = strlen($chars) - 1;

		$result = '';

		for ($i = $length; $i > 0; --$i) {
			$result .= $chars[mt_rand(0, $maxCharsIndex)];
		}

		return $result;
	}

}
