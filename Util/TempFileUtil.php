<?php

namespace Craue\FormFlowBundle\Util;

/**
 * Keeps track of temporary files to be able to remove them when no longer needed.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class TempFileUtil {

	private static $tempFiles = array();

	private function __construct() {}

	/**
	 * @param string $tempFile Path to a file.
	 */
	public static function addTempFile($tempFile) {
		self::$tempFiles[] = $tempFile;
	}

	/**
	 * Removes all previously added files from disk.
	 */
	public static function removeTempFiles() {
		foreach (self::$tempFiles as $tempFile) {
			if (is_file($tempFile)) {
				unlink($tempFile);
			}
		}

		self::$tempFiles = array();
	}

}
