<?php
/**
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @copyright 2011 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Craue\FormFlowBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class PreBind extends Event
{
    protected $formData;

    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    public function getFormData()
    {
        return $this->formData;
    }
}