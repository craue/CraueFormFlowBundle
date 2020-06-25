<?php

namespace Craue\FormFlowBundle\Storage;

use Craue\FormFlowBundle\Form\FormFlowInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
interface DataManagerInterface
{

    /**
     * @var string Key for storing data of all flows.
     */
    const STORAGE_ROOT = 'craue_form_flow';


    /**
     * Saves data of the given flow.
     * @param FormFlowInterface $flow
     * @param array $data
     */
    public function save(FormFlowInterface $flow, array $data);

    /**
     * Checks if data exists for a given flow.
     * @param FormFlowInterface $flow
     * @return bool
     */
    public function exists(FormFlowInterface $flow);

    /**
     * Loads data of the given flow.
     * @param FormFlowInterface $flow
     * @return array
     */
    public function load(FormFlowInterface $flow);

    /**
     * Drops data of the given flow.
     * @param FormFlowInterface $flow
     */
    public function drop(FormFlowInterface $flow);

}
