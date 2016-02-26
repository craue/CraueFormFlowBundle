<?php

namespace Craue\FormFlowBundle\Tests\Resources;

use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2016 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class TranslationsTest extends \PHPUnit_Framework_TestCase {

	const DEFAULT_LOCALE = 'en';

	/**
	 * @dataProvider dataYamlTranslationFileIsValid
	 */
	public function testYamlTranslationFileIsValid($filePath) {
		Yaml::parse(file_get_contents($filePath));
	}

	public function dataYamlTranslationFileIsValid() {
		return array_map(function ($filePath) {
			return (array) $filePath;
		}, $this->getTranslationFilePaths());
	}

	/**
	 * Ensure that translation files contain only message keys also available in the default translation.
	 *
	 * It's ok for a translation file to contain not all of the default translation's keys, since this happens when new functionality is
	 * added and the translations will be completed later.
	 * But it's not ok for a translation file to contain keys that are not available in the default translation.
	 */
	public function testYamlTranslationFilesContainNoUnknownKeys() {
		$loader = new YamlFileLoader();
		$translations = array();

		foreach ($this->getTranslationFilePaths() as $filePath) {
			list($domain, $locale) = explode('.', basename($filePath));
			$catalogue = $loader->load($filePath, $locale, $domain);
			$translations[$domain][$locale] = array_keys($catalogue->all($domain));
		}

		$this->assertNotEmpty($translations, 'No translations found. Check the path to translation files.');

		foreach ($translations as $domain => $locales) {
			foreach ($locales as $locale => $keys) {
				if ($locale === self::DEFAULT_LOCALE) {
					continue;
				}

				$this->assertEquals(array(), array_diff($keys, $translations[$domain][self::DEFAULT_LOCALE]),
						sprintf('Translation "%s" contains message keys not available in translation "%s".', $locale, self::DEFAULT_LOCALE));
			}
		}
	}

	private function getTranslationFilePaths() {
		return glob(__DIR__ . '/../../Resources/translations/*.yml');
	}

}
