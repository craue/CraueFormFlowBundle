<?php

namespace Craue\FormFlowBundle\Exception;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class AllStepsSkippedException extends \RuntimeException {

	public function __construct() {
		parent::__construct('All steps are marked as skipped. Please check the flow to make sure at least one step is not skipped.');
	}

}
