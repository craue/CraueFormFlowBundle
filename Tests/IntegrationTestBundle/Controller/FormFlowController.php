<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Controller;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Issue149Data;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Issue64Data;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\PhotoUpload;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\RevalidatePreviousStepsData;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Topic;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Vehicle;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\CreateTopicFlow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\CreateVehicleFlow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\Demo1Flow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\Issue64Flow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\Issue87Flow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\Issue149Flow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\Issue303Flow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\OnlyOneStepFlow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\PhotoUploadFlow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\RemoveSecondStepSkipMarkOnResetFlow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\RevalidatePreviousStepsFlow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\SkipFirstStepUsingClosureFlow;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowController extends AbstractController {

	/**
	 * @Route("/create-topic/", name="_FormFlow_createTopic")
	 */
	public function createTopicAction() {
		return $this->processFlow(new Topic(), $this->get(CreateTopicFlow::class));
	}

	/**
	 * @Route("/create-topic-redirect-after-submit/", name="_FormFlow_createTopic_redirectAfterSubmit")
	 */
	public function createTopicRedirectAfterSubmitAction() {
		$flow = $this->get(CreateTopicFlow::class);
		$flow->setAllowDynamicStepNavigation(false);
		$flow->setAllowRedirectAfterSubmit(true);

		return $this->processFlow(new Topic(), $flow);
	}

	/**
	 * @Route("/create-vehicle/", name="_FormFlow_createVehicle")
	 */
	public function createVehicleAction() {
		return $this->processFlow(new Vehicle(), $this->get(CreateVehicleFlow::class));
	}

	/**
	 * @Route("/demo1/", name="_FormFlow_demo1")
	 */
	public function demo1Action() {
		return $this->processFlow(new \stdClass(), $this->get(Demo1Flow::class));
	}

	/**
	 * @Route("/issue64/", name="_FormFlow_issue64")
	 */
	public function issue64Action() {
		return $this->processFlow(new Issue64Data(), $this->get(Issue64Flow::class));
	}

	/**
	 * No trailing slash here to add the step only when needed.
	 * @Route("/issue87/{step}", defaults={"step"=1}, name="_FormFlow_issue87")
	 */
	public function issue87Action() {
		return $this->processFlow(new \stdClass(), $this->get(Issue87Flow::class));
	}

	/**
	 * @Route("/issue149/", name="_FormFlow_issue149")
	 */
	public function issue149Action() {
		return $this->processFlow(new Issue149Data(), $this->get(Issue149Flow::class));
	}

	/**
	 * @Route("/issue303/", name="_FormFlow_issue303")
	 */
	public function issue303Action() {
		return $this->processFlow(new \stdClass(), $this->get(Issue303Flow::class));
	}

	/**
	 * @Route("/revalidatePreviousSteps/enabled/", defaults={"enabled"=true}, name="_FormFlow_revalidatePreviousSteps_enabled")
	 * @Route("/revalidatePreviousSteps/disabled/", defaults={"enabled"=false}, name="_FormFlow_revalidatePreviousSteps_disabled")
	 */
	public function revalidatePreviousStepsAction($enabled) {
		$flow = $this->get(RevalidatePreviousStepsFlow::class);
		$flow->setRevalidatePreviousSteps($enabled);

		return $this->processFlow(new RevalidatePreviousStepsData(), $flow);
	}

	/**
	 * @Route("/skipFirstStepUsingClosure/", name="_FormFlow_skipFirstStepUsingClosure")
	 */
	public function skipFirstStepUsingClosureAction() {
		return $this->processFlow(new \stdClass(), $this->get(SkipFirstStepUsingClosureFlow::class));
	}

	/**
	 * @Route("/removeSecondStepSkipMarkOnReset/", name="_FormFlow_removeSecondStepSkipMarkOnReset")
	 */
	public function removeSecondStepSkipMarkOnResetAction() {
		return $this->processFlow(new \stdClass(), $this->get(RemoveSecondStepSkipMarkOnResetFlow::class));
	}

	/**
	 * @Route("/onlyOneStep/", name="_FormFlow_onlyOneStep")
	 */
	public function onlyOneStepAction() {
		return $this->processFlow(new \stdClass(), $this->get(OnlyOneStepFlow::class));
	}

	/**
	 * @Route("/photoUpload/", name="_FormFlow_photoUpload")
	 */
	public function photoUploadAction() {
		return $this->processFlow(new PhotoUpload(), $this->get(PhotoUploadFlow::class),
				'@IntegrationTest/FormFlow/photoUpload.html.twig');
	}

	/**
	 * @Route("/usualForm/", name="_FormFlow_usualForm")
	 */
	public function usualFormAction() {
		return $this->processFlow(new Topic(), $this->get(CreateTopicFlow::class),
				'@IntegrationTest/FormFlow/usualForm.html.twig', ['usualForm' => $this->createFormBuilder()->getForm()->createView()]);
	}

	protected function processFlow($formData, FormFlow $flow, $template = '@IntegrationTest/layout_flow.html.twig', array $templateParameters = []) {
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

		return $this->render($template, array_merge($templateParameters, [
			'form' => $form->createView(),
			'flow' => $flow,
			'formData' => $formData,
		]));
	}

}
