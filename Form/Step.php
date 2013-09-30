<?php

namespace Craue\FormFlowBundle\Form;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2013 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Step implements StepInterface {

	/**
	 * @var integer
	 */
	protected $number;

	/**
	 * @var string|null
	 */
	protected $label;

	/**
	 * @var FormTypeInterface|string|null
	 */
	protected $type;

	/**
	 * @var callable|null
	 */
	private $skipFunction;

	/**
	 * @var boolean|null Is only null if not yet evaluated.
	 */
	private $skipped = null;

	public static function createFromConfig($number, array $config) {
		$step = new static();

		$step->setNumber($number);
		$step->setLabel(array_key_exists('label', $config) ? $config['label'] : null);
		$step->setType(array_key_exists('type', $config) ? $config['type'] : null);
		$step->setSkip(array_key_exists('skip', $config) ? $config['skip'] : false);

		return $step;
	}

	/**
	 * @param integer $number
	 */
	public function setNumber($number) {
		if (is_int($number)) {
			$this->number = $number;

			return;
		}

		throw new InvalidTypeException($number, 'integer');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getNumber() {
		return $this->number;
	}

	/**
	 * @param string|null $label
	 */
	public function setLabel($label) {
		if ($label === null || is_string($label)) {
			$this->label = $label;

			return;
		}

		throw new InvalidTypeException($label, array('null', 'string'));
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @param FormTypeInterface|string|null $type
	 * @throws InvalidTypeException
	 */
	public function setType($type) {
		if ($type === null || is_string($type) || $type instanceof FormTypeInterface || $type InstanceOf FormInterface) {
			$this->type = $type;

			return;
		}

		throw new InvalidTypeException($type, array('null', 'string', 'Symfony\Component\Form\FormTypeInterface', 'Symfony\Component\Form\FormInterface'));
	}

	/**
	 * {@inheritDoc}
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param boolean|callable $skip
	 * @throws InvalidTypeException
	 */
	public function setSkip($skip) {
		if (is_bool($skip)) {
			$this->skipFunction = null;
			$this->skipped = $skip;

			return;
		}

		if (is_callable($skip)) {
			$this->skipFunction = $skip;
			$this->skipped = null;

			return;
		}

		throw new InvalidTypeException($skip, array('boolean', 'callable'));
	}

	/**
	 * {@inheritDoc}
	 */
	public function evaluateSkipping($estimatedCurrentStepNumber, FormFlowInterface $flow) {
		if ($this->skipFunction !== null) {
			$returnValue = call_user_func_array($this->skipFunction, array($estimatedCurrentStepNumber, $flow));

			if (!is_bool($returnValue)) {
				throw new \RuntimeException('The callable did not return a boolean value.');
			}

			$this->skipped = $returnValue;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSkipped() {
		return $this->skipped;
	}

}
