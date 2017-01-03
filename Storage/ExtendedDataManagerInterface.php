<?php

namespace Craue\FormFlowBundle\Storage;

/**
 * Extends the base {@link DataManagerInterface} by methods which may be used for custom flow management.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
interface ExtendedDataManagerInterface extends DataManagerInterface {

	/**
	 * Note: This method may be used for custom flow management.
	 * @return string[] Distinct names of flows (which may have data for more than one instance).
	 */
	function listFlows();

	/**
	 * Note: This method may be used for custom flow management.
	 * @param $name Name of the flow.
	 * @return string[] Instances of flows with the given name.
	 */
	function listInstances($name);

	/**
	 * Drops data of all flows.
	 * Note: This method may be used for custom flow management.
	 */
	function dropAll();

}
