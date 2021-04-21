<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2021 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
trait LogEventCallsTrait {

	/**
	 * @var string[]
	 */
	private $loggedEventCalls = [];

	private function clearLoggedEventCalls() : void {
		$this->loggedEventCalls = [];
	}

	private function logEventCall($name) : void {
		$this->loggedEventCalls[] = $name;
	}

	public function getLoggedEventCalls() : array {
		return $this->loggedEventCalls;
	}

}
