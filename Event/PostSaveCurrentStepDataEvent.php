<?php

namespace Craue\FormFlowBundle\Event;

use Craue\FormFlowBundle\Form\FormFlowInterface;

/**
 * Is called once after current step data are save to the session
 *
 * @author Cyril Mouttet <cyril.mouttet@gmail.com>
 * @copyright 2011-2016 Cyril Mouttet
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PostSaveCurrentStepDataEvent extends FormFlowEvent {

	/**
	 * @var array
	 */
	protected $stepData;

	/**
	 * @var integer
	 */
	protected $stepNumber;

	/**
	 * @param FormFlowInterface $flow
	 * @param array $stepData
	 * @param integer $stepNumber
	 */
	public function __construct(FormFlowInterface $flow, $stepData, $stepNumber) {
		$this->flow = $flow;
		$this->stepData = $stepData;
		$this->stepNumber = $stepNumber;
	}

	/**
	 * @return mixed
	 */
	public function getStepData() {
		return $this->stepData;
	}

	/**
	 * @return integer
	 */
	public function getStepNumber() {
		return $this->stepNumber;
	}

}
