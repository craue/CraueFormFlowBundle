<?php

namespace Craue\FormFlowBundle\Event;

use Craue\FormFlowBundle\Form\FormFlowInterface;

/**
 * @internal
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2021 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
trait FormFlowEventTrait {

    /**
     * @var FormFlowInterface
     */
    protected $flow;

    /**
     * @param FormFlowInterface $flow
     */
    public function __construct(FormFlowInterface $flow) {
        $this->flow = $flow;
    }

    /**
     * @return FormFlowInterface
     */
    public function getFlow() {
        return $this->flow;
    }
}
