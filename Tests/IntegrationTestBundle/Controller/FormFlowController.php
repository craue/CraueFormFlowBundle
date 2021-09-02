<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Controller;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Issue149Data;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Issue64Data;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\PhotoCollection;
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
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\PhotoCollectionUploadFlow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\PhotoUploadFlow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\RemoveSecondStepSkipMarkOnResetFlow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\RevalidatePreviousStepsFlow;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\SkipFirstStepUsingClosureFlow;
use Craue\FormFlowBundle\Util\FormFlowUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2021 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowController extends AbstractController {

	/**
	 * @var FormFlowUtil
	 */
	private $formFlowUtil;

	/**
	 * @var Environment
	 */
	private $twig;

	public function __construct(FormFlowUtil $formFlowUtil, Environment $twig) {
		$this->formFlowUtil = $formFlowUtil;
		$this->twig = $twig;
	}

	/**
	 * @Route("/create-topic/", name="_FormFlow_createTopic")
	 */
	public function createTopicAction(Request $request, CreateTopicFlow $flow) {
		return $this->processFlow($request, new Topic(), $flow);
	}

	/**
	 * @Route("/create-topic-redirect-after-submit/", name="_FormFlow_createTopic_redirectAfterSubmit")
	 */
	public function createTopicRedirectAfterSubmitAction(Request $request, CreateTopicFlow $flow) {
		$flow->setAllowDynamicStepNavigation(false);
		$flow->setAllowRedirectAfterSubmit(true);

		return $this->processFlow($request, new Topic(), $flow);
	}

	/**
	 * @Route("/create-vehicle/", name="_FormFlow_createVehicle")
	 */
	public function createVehicleAction(Request $request, CreateVehicleFlow $flow) {
		return $this->processFlow($request, new Vehicle(), $flow);
	}

	/**
	 * @Route("/demo1/", name="_FormFlow_demo1")
	 */
	public function demo1Action(Request $request, Demo1Flow $flow) {
		return $this->processFlow($request, new \stdClass(), $flow);
	}

	/**
	 * @Route("/issue64/", name="_FormFlow_issue64")
	 */
	public function issue64Action(Request $request, Issue64Flow $flow) {
		return $this->processFlow($request, new Issue64Data(), $flow);
	}

	/**
	 * No trailing slash here to add the step only when needed.
	 * @Route("/issue87/{step}", defaults={"step"=1}, name="_FormFlow_issue87")
	 */
	public function issue87Action(Request $request, Issue87Flow $flow) {
		return $this->processFlow($request, new \stdClass(), $flow);
	}

	/**
	 * @Route("/issue149/", name="_FormFlow_issue149")
	 */
	public function issue149Action(Request $request, Issue149Flow $flow) {
		return $this->processFlow($request, new Issue149Data(), $flow);
	}

	/**
	 * @Route("/issue303/", name="_FormFlow_issue303")
	 */
	public function issue303Action(Request $request, Issue303Flow $flow) {
		return $this->processFlow($request, new \stdClass(), $flow);
	}

	/**
	 * @Route("/revalidatePreviousSteps/enabled/", defaults={"enabled"=true}, name="_FormFlow_revalidatePreviousSteps_enabled")
	 * @Route("/revalidatePreviousSteps/disabled/", defaults={"enabled"=false}, name="_FormFlow_revalidatePreviousSteps_disabled")
	 */
	public function revalidatePreviousStepsAction(Request $request, RevalidatePreviousStepsFlow $flow, $enabled) {
		$flow->setRevalidatePreviousSteps($enabled);

		return $this->processFlow($request, new RevalidatePreviousStepsData(), $flow);
	}

	/**
	 * @Route("/skipFirstStepUsingClosure/", name="_FormFlow_skipFirstStepUsingClosure")
	 */
	public function skipFirstStepUsingClosureAction(Request $request, SkipFirstStepUsingClosureFlow $flow) {
		return $this->processFlow($request, new \stdClass(), $flow);
	}

	/**
	 * @Route("/removeSecondStepSkipMarkOnReset/", name="_FormFlow_removeSecondStepSkipMarkOnReset")
	 */
	public function removeSecondStepSkipMarkOnResetAction(Request $request, RemoveSecondStepSkipMarkOnResetFlow $flow) {
		return $this->processFlow($request, new \stdClass(), $flow);
	}

	/**
	 * @Route("/onlyOneStep/", name="_FormFlow_onlyOneStep")
	 */
	public function onlyOneStepAction(Request $request, OnlyOneStepFlow $flow) {
		return $this->processFlow($request, new \stdClass(), $flow);
	}

	/**
	 * @Route("/photoUpload/", name="_FormFlow_photoUpload")
	 */
	public function photoUploadAction(Request $request, PhotoUploadFlow $flow) {
		return $this->processFlow($request, new PhotoUpload(), $flow,
				'@IntegrationTest/FormFlow/photoUpload.html.twig');
	}

	/**
	 * @Route("/photoCollectionUpload/", name="_FormFlow_photoCollectionUpload")
	 */
	public function photoCollectionUploadAction(Request $request, PhotoCollectionUploadFlow $flow) {
		return $this->processFlow($request, new PhotoCollection(), $flow,
				'@IntegrationTest/FormFlow/photoCollectionUpload.html.twig');
	}

	/**
	 * @Route("/usualForm/", name="_FormFlow_usualForm")
	 */
	public function usualFormAction(Request $request, CreateTopicFlow $flow, FormFactoryInterface $formFactory) {
		return $this->processFlow($request, new Topic(), $flow,
				'@IntegrationTest/FormFlow/usualForm.html.twig', ['usualForm' => $formFactory->create()->createView()]);
	}

	protected function processFlow(Request $request, $formData, FormFlow $flow, $template = '@IntegrationTest/layout_flow.html.twig', array $templateParameters = []) {
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
			$params = $this->formFlowUtil->addRouteParameters(array_merge($request->query->all(),
					$request->attributes->get('_route_params')), $flow);

			return $this->redirect($this->generateUrl($request->attributes->get('_route'), $params));
		}

		return new Response($this->twig->render($template, array_merge($templateParameters, [
			'form' => $form->createView(),
			'flow' => $flow,
			'formData' => $formData,
		])));
	}

}
