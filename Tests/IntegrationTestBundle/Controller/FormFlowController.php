<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Controller;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Issue149Data;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Issue64Data;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\PhotoUpload;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\RevalidatePreviousStepsData;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Topic;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Vehicle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowController extends Controller {

	/**
	 * @Route("/create-topic/", name="_FormFlow_createTopic")
	 */
	public function createTopicAction() {
		return $this->processFlow(new Topic(), $this->get('integrationTestBundle.form.flow.createTopic'));
	}

	/**
	 * @Route("/create-topic-redirect-after-submit/", name="_FormFlow_createTopic_redirectAfterSubmit")
	 */
	public function createTopicRedirectAfterSubmitAction() {
		$flow = $this->get('integrationTestBundle.form.flow.createTopic');
		$flow->setAllowDynamicStepNavigation(false);
		$flow->setAllowRedirectAfterSubmit(true);

		return $this->processFlow(new Topic(), $flow);
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
	 * @Route("/issue149/", name="_FormFlow_issue149")
	 */
	public function issue149Action() {
		return $this->processFlow(new Issue149Data(), $this->get('integrationTestBundle.form.flow.issue149'));
	}

	/**
	 * @Route("/revalidatePreviousSteps/enabled/", defaults={"enabled"=true}, name="_FormFlow_revalidatePreviousSteps_enabled")
	 * @Route("/revalidatePreviousSteps/disabled/", defaults={"enabled"=false}, name="_FormFlow_revalidatePreviousSteps_disabled")
	 */
	public function revalidatePreviousStepsAction($enabled) {
		$flow = $this->get('integrationTestBundle.form.flow.revalidatePreviousSteps');
		$flow->setRevalidatePreviousSteps($enabled);

		return $this->processFlow(new RevalidatePreviousStepsData(), $flow);
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

	/**
	 * @Route("/onlyOneStep/", name="_FormFlow_onlyOneStep")
	 */
	public function onlyOneStepAction() {
		return $this->processFlow(new \stdClass(), $this->get('integrationTestBundle.form.flow.onlyOneStep'));
	}

	/**
	 * @Route("/photoUpload/", name="_FormFlow_photoUpload")
	 */
	public function photoUploadAction() {
		return $this->processFlow(new PhotoUpload(), $this->get('integrationTestBundle.form.flow.photoUpload'),
				'@IntegrationTest/FormFlow/photoUpload.html.twig');
	}

	/**
	 * @Route("/usualForm/", name="_FormFlow_usualForm")
	 */
	public function usualFormAction() {
		return $this->processFlow(new Topic(), $this->get('integrationTestBundle.form.flow.createTopic'),
				'@IntegrationTest/FormFlow/usualForm.html.twig', array('usualForm' => $this->createFormBuilder()->getForm()->createView()));
	}

	protected function processFlow($formData, FormFlow $flow, $template = '@IntegrationTest/layout_flow.html.twig', array $templateParameters = array()) {
		$flow->bind($formData);

		$form = $submittedForm = $flow->createForm();
		if ($flow->isValid($submittedForm)) {
			$flow->saveCurrentStepData($submittedForm);

			if ($flow->nextStep()) {
				// create form for next step
				$form = $flow->createForm();
			} else {
				// flow finished
				$flow->reset();

				return new JsonResponse($formData);
			}
		}

		if ($flow->redirectAfterSubmit($submittedForm)) {
			$request = $this->get('request_stack')->getCurrentRequest();
			$params = $this->get('craue_formflow_util')->addRouteParameters(array_merge($request->query->all(),
					$request->attributes->get('_route_params')), $flow);

			return $this->redirect($this->generateUrl($request->attributes->get('_route'), $params));
		}

		return $this->render($template, array_merge($templateParameters, array(
			'form' => $form->createView(),
			'flow' => $flow,
			'formData' => $formData,
		)));
	}

}
