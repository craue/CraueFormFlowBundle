<?php

namespace Craue\FormFlowBundle\Tests\EventListener;

use Craue\FormFlowBundle\EventListener\FlowExpiredEventListener;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2019 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FlowExpiredEventListenerTest extends EventListenerNeedsTranslatorTest {

	protected function getListener() {
		return new FlowExpiredEventListener();
	}

}
