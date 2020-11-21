<?php

namespace Craue\FormFlowBundle\Event;

use Craue\FormFlowBundle\Form\FormFlowInterface;

trait FormFlowEventTrait
{
    /**
     * @var FormFlowInterface
     */
    protected $flow;

    /**
     * @param FormFlowInterface $flow
     */
    public function __construct(FormFlowInterface $flow)
    {
        $this->flow = $flow;
    }

    /**
     * @return FormFlowInterface
     */
    public function getFlow()
    {
        return $this->flow;
    }
}
