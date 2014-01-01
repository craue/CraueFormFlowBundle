<?php

namespace Craue\FormFlowBundle\Event;

use Craue\FormFlowBundle\Form\FormFlowInterface;

/**
 * Is called once prior to binding any (neither saved nor request) data.
 * You can use this event to define steps to skip prior to determinating the current step, e.g. based on custom
 * session data.
 *
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PreBindEvent extends FormFlowEvent {

}
