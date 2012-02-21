<?php
namespace Craue\FormFlowBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @copyright 2011-2012 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 * Is called once for the current step after binding the request.
 */
class PostBindRequestEvent extends Event
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var integer
     */
    private $step;

    /**
     * @param array $data
     * @param integer $step
     */
    public function __construct($data, $step)
    {
        $this->data = $data;
        $this->step = $step;
    }

    /**
     * Return form data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return integer
     */
    public function getStep()
    {
        return $this->step;
    }
}