<?php
/**
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @copyright 2011 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Craue\FormFlowBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class PostBindRequest extends Event
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var integer
     */
    protected $step;

    /**
     * Constructor
     *
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
     * @return mixed
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