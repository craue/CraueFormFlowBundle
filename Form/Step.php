<?php

namespace Craue\FormFlowBundle\Form;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Craue\FormFlowBundle\Exception\StepLabelCallableInvalidReturnValueException;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Step implements StepInterface {

	/**
	 * @var int
	 */
	protected $number;

	/**
	 * @var string|StepLabel|null
	 */
	protected $label = null;

	/**
	 * @var FormTypeInterface|string|null
	 */
	protected $formType = null;

	/**
	 * @var array
	 */
	protected $formOptions = array();

	/**
	 * @var callable|null
	 */
	private $skipFunction = null;

	/**
	 * @var bool|null Is only null if not yet evaluated.
	 */
	private $skipped = false;

	public static function createFromConfig($number, array $config) {
		$step = new static();

		$step->setNumber($number);

		foreach ($config as $key => $value) {
			switch ($key) {
				case 'label':
					$step->setLabel($value);
					break;
				case 'type':
					@trigger_error('Step config option "type" is deprecated since version 3.0. Use "form_type" instead.', E_USER_DEPRECATED);
				case 'form_type':
					$step->setFormType($value);
					break;
				case 'form_options':
					$step->setFormOptions($value);
					break;
				case 'skip':
					$step->setSkip($value);
					break;
				default:
					throw new \InvalidArgumentException(sprintf('Invalid step config option "%s" given.', $key));
			}
		}

		return $step;
	}

	/**
	 * @param int $number
	 */
	public function setNumber($number) {
		if (is_int($number)) {
			$this->number = $number;

			return;
		}

		throw new InvalidTypeException($number, 'int');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getNumber() {
		return $this->number;
	}

	/**
	 * @param string|StepLabel|null $label
	 */
	public function setLabel($label) {
		if (is_string($label)) {
			$this->label = StepLabel::createStringLabel($label);

			return;
		}

		if ($label === null || $label instanceof StepLabel) {
			$this->label = $label;

			return;
		}

		throw new InvalidTypeException($label, array('null', 'string', 'Craue\FormFlowBundle\Form\StepLabel'));
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLabel() {
		try {
			return $this->label !== null ? $this->label->getText() : null;
		} catch (StepLabelCallableInvalidReturnValueException $e) {
			throw new \RuntimeException(sprintf('The label callable for step %d did not return a string or null value.',
					$this->number));
		}
	}

	/**
	 * @param FormTypeInterface|string|null $type
	 * @throws InvalidTypeException
	 */
	public function setFormType($formType) {
		if ($formType === null || is_string($formType) || $formType instanceof FormTypeInterface) {
			$this->formType = $formType;

			return;
		}

		throw new InvalidTypeException($formType, array('null', 'string', 'Symfony\Component\Form\FormTypeInterface'));
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFormType() {
		return $this->formType;
	}

	/**
	 * @param array $formOptions
	 */
	public function setFormOptions($formOptions) {
		if (is_array($formOptions)) {
			$this->formOptions = $formOptions;

			return;
		}

		throw new InvalidTypeException($formOptions, 'array');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFormOptions() {
		return $this->formOptions;
	}

	/**
	 * @param bool|callable $skip
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

		throw new InvalidTypeException($skip, array('bool', 'callable'));
	}

	/**
	 * {@inheritDoc}
	 */
	public function evaluateSkipping($estimatedCurrentStepNumber, FormFlowInterface $flow) {
		if ($this->skipFunction !== null) {
			$returnValue = call_user_func_array($this->skipFunction, array($estimatedCurrentStepNumber, $flow));

			if (!is_bool($returnValue)) {
				throw new \RuntimeException(sprintf('The skip callable for step %d did not return a boolean value.',
						$this->number));
			}

			$this->skipped = $returnValue;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSkipped() {
		return $this->skipped === true;
	}

}
