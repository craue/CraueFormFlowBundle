<?php

namespace Craue\FormFlowBundle\Form;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2013 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class EmptyStep implements StepInterface {

	/**
	 * @var integer
	 */
	protected $number;

	public function __construct($number) {
		$this->number = $number;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getNumber() {
		return $this->number;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLabel() {
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getType() {
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSkipped() {
		return false;
	}

}
