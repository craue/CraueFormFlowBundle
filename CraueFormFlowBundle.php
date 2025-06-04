<?php

namespace Craue\FormFlowBundle;

use Craue\FormFlowBundle\Util\TempFileUtil;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2024 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CraueFormFlowBundle extends Bundle {

	/**
	 * @return void
	 */
	public function boot() {
		/*
		 * Removes all temporary files created while handling file uploads.
		 * Use a shutdown function to clean up even in case of a fatal error.
		 */
		register_shutdown_function(function() : void {
			TempFileUtil::removeTempFiles();
		});
	}

}
