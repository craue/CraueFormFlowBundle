<?php

namespace Craue\FormFlowBundle\Form;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Craue\FormFlowBundle\Exception\StepLabelCallableInvalidReturnValueException;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class StepLabel {

	/**
	 * @var bool If <code>$value</code> is callable.
	 */
	private $callable;

	/**
	 * @var string|callable|null
	 */
	private $value = null;

	/**
	 * @param string|null $value
	 */
	public static function createStringLabel($value) {
		return new static($value);
	}

	/**
	 * @param callable $value
	 */
	public static function createCallableLabel($value) {
		return new static($value, true);
	}

	/**
	 * @return string|null
	 */
	public function getText() {
		if ($this->callable) {
			$returnValue = call_user_func($this->value);

			if ($returnValue === null || is_string($returnValue)) {
				return $returnValue;
			}

			throw new StepLabelCallableInvalidReturnValueException();
		}

		return $this->value;
	}

	/**
	 * @param string|callable|null $value
	 * @param bool $callable
	 */
	private function __construct($value, $callable = false) {
		$this->setValue($value, $callable);
	}

	/**
	 * @param string|callable|null $value
	 * @param bool $callable
	 */
	private function setValue($value, $callable = false) {
		if ($callable) {
			if (!is_callable($value)) {
				throw new InvalidTypeException($value, array('callable'));
			}
		} else {
			if ($value !== null && !is_string($value)) {
				throw new InvalidTypeException($value, array('null', 'string'));
			}
		}

		$this->callable = $callable;
		$this->value = $value;
	}

}
