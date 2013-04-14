<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Controller;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Vehicle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2013 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowController extends Controller {

	/**
	 * @Route("/create-vehicle/", name="_FormFlow_createVehicle")
	 * @Template("IntegrationTestBundle:FormFlow:createVehicle.html.twig")
	 */
	public function createVehicleAction() {
		return $this->processFlow(new Vehicle(), $this->get('integrationTestBundle.form.flow.createVehicle'));
	}

	protected function processFlow($formData, FormFlow $flow) {
		$flow->bind($formData);

		$form = $flow->createForm($formData);
		if ($flow->isValid($form)) {
			$flow->saveCurrentStepData();

			if ($flow->nextStep()) {
				// create form for next step
				$form = $flow->createForm($formData);
			} else {
				// flow finished
				$flow->reset();

				return new JsonResponse($formData);
			}
		}

		return array(
			'form' => $form->createView(),
			'flow' => $flow,
			'formData' => $formData,
		);
	}

}
