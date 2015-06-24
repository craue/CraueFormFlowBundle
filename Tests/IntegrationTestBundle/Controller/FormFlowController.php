<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Controller;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Issue64Data;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Topic;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Vehicle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowController extends Controller {

	/**
	 * @Route("/create-topic/start/", name="_FormFlow_createTopic_start")
	 */
	public function createTopicStartAction() {
		$flow = $this->get('integrationTestBundle.form.flow.createTopic');
		$flow->reset();

		return $this->redirect($this->generateUrl('_FormFlow_createTopic'));
	}

	/**
	 * @Route("/create-topic/", name="_FormFlow_createTopic")
	 */
	public function createTopicAction() {
		return $this->processFlow(new Topic(), $this->get('integrationTestBundle.form.flow.createTopic'));
	}

	/**
	 * @Route("/create-vehicle/", name="_FormFlow_createVehicle")
	 */
	public function createVehicleAction() {
		return $this->processFlow(new Vehicle(), $this->get('integrationTestBundle.form.flow.createVehicle'));
	}

	/**
	 * @Route("/demo1/", name="_FormFlow_demo1")
	 */
	public function demo1Action() {
		return $this->processFlow(new \stdClass(), $this->get('integrationTestBundle.form.flow.demo1'));
	}

	/**
	 * @Route("/issue64/", name="_FormFlow_issue64")
	 */
	public function issue64Action() {
		return $this->processFlow(new Issue64Data(), $this->get('integrationTestBundle.form.flow.issue64'));
	}

	/**
	 * No trailing slash here to add the step only when needed.
	 * @Route("/issue87/{step}", defaults={"step"=1}, name="_FormFlow_issue87")
	 */
	public function issue87Action() {
		return $this->processFlow(new \stdClass(), $this->get('integrationTestBundle.form.flow.issue87'));
	}

	/**
	 * @Route("/skipFirstStepUsingClosure/", name="_FormFlow_skipFirstStepUsingClosure")
	 */
	public function skipFirstStepUsingClosureAction() {
		return $this->processFlow(new \stdClass(), $this->get('integrationTestBundle.form.flow.skipFirstStepUsingClosure'));
	}

	/**
	 * @Route("/removeSecondStepSkipMarkOnReset/", name="_FormFlow_removeSecondStepSkipMarkOnReset")
	 */
	public function removeSecondStepSkipMarkOnResetAction() {
		return $this->processFlow(new \stdClass(), $this->get('integrationTestBundle.form.flow.removeSecondStepSkipMarkOnReset'));
	}

	protected function processFlow($formData, FormFlow $flow, $template = 'IntegrationTestBundle::layout_flow.html.twig') {
		$flow->bind($formData);

		$form = $flow->createForm();
		if ($flow->isValid($form)) {
			$flow->saveCurrentStepData($form);

			if ($flow->nextStep()) {
				// create form for next step
				$form = $flow->createForm();
			} else {
				// flow finished
				$flow->reset();

				return new JsonResponse($formData);
			}
		}

		return $this->render($template, array(
			'form' => $form->createView(),
			'flow' => $flow,
			'formData' => $formData,
		));
	}

}
