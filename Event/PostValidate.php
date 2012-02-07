<?php
/**
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @copyright 2011 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Craue\FormFlowBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class PostValidate extends Event
{
    /**
     * @var array
     */
    private $formData;

    /**
     * @param array $formData
     */
    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    /**
     * @return array
     */
    public function getFormData()
    {
        return $this->formData;
    }
}