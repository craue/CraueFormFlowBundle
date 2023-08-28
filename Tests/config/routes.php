<?php

use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Controller\FormFlowController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
	$routes->add('_FormFlow_createTopic', '/create-topic/')
		->controller([FormFlowController::class, 'createTopicAction'])
	;
	$routes->add('_FormFlow_createTopic_redirectAfterSubmit', '/create-topic-redirect-after-submit/')
		->controller([FormFlowController::class, 'createTopicRedirectAfterSubmitAction'])
	;
	$routes->add('_FormFlow_createVehicle', '/create-vehicle/')
		->controller([FormFlowController::class, 'createVehicleAction'])
	;
	$routes->add('_FormFlow_demo1', '/demo1/')
		->controller([FormFlowController::class, 'demo1Action'])
	;
	$routes->add('_FormFlow_issue64', '/issue64/')
		->controller([FormFlowController::class, 'issue64Action'])
	;
	$routes->add('_FormFlow_issue87', '/issue87/{step}') // No trailing slash here to add the step only when needed.
		->controller([FormFlowController::class, 'issue87Action'])
		->defaults(['step' => 1])
	;
	$routes->add('_FormFlow_issue149', '/issue149/')
		->controller([FormFlowController::class, 'issue149Action'])
	;
	$routes->add('_FormFlow_issue303', '/issue303/')
		->controller([FormFlowController::class, 'issue303Action'])
	;
	$routes->add('_FormFlow_revalidatePreviousSteps_enabled', '/revalidatePreviousSteps/enabled/')
		->controller([FormFlowController::class, 'revalidatePreviousStepsAction'])
		->defaults(['enabled' => true])
	;
	$routes->add('_FormFlow_revalidatePreviousSteps_disabled', '/revalidatePreviousSteps/disabled/')
		->controller([FormFlowController::class, 'revalidatePreviousStepsAction'])
		->defaults(['enabled' => false])
	;
	$routes->add('_FormFlow_skipFirstStepUsingClosure', '/skipFirstStepUsingClosure/')
		->controller([FormFlowController::class, 'skipFirstStepUsingClosureAction'])
	;
	$routes->add('_FormFlow_removeSecondStepSkipMarkOnReset', '/removeSecondStepSkipMarkOnReset/')
		->controller([FormFlowController::class, 'removeSecondStepSkipMarkOnResetAction'])
	;
	$routes->add('_FormFlow_onlyOneStep', '/onlyOneStep/')
		->controller([FormFlowController::class, 'onlyOneStepAction'])
	;
	$routes->add('_FormFlow_photoUpload', '/photoUpload/')
		->controller([FormFlowController::class, 'photoUploadAction'])
	;
	$routes->add('_FormFlow_photoCollectionUpload', '/photoCollectionUpload/')
		->controller([FormFlowController::class, 'photoCollectionUploadAction'])
	;
	$routes->add('_FormFlow_usualForm', '/usualForm/')
		->controller([FormFlowController::class, 'usualFormAction'])
	;
};
