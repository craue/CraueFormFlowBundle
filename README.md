# Information

[![Build Status](https://travis-ci.org/craue/CraueFormFlowBundle.svg?branch=master)](https://travis-ci.org/craue/CraueFormFlowBundle)
[![Coverage Status](https://coveralls.io/repos/github/craue/CraueFormFlowBundle/badge.svg?branch=master)](https://coveralls.io/github/craue/CraueFormFlowBundle?branch=master)

CraueFormFlowBundle provides a facility for building and handling multi-step forms in your Symfony project.

Features:
- navigation (next, back, start over)
- step labels
- skipping of steps
- different validation group for each step
- handling of file uploads
- dynamic step navigation (optional)
- redirect after submit (a.k.a. "Post/Redirect/Get", optional)

A live demo showcasing these features is available at http://craue.de/symfony-playground/en/CraueFormFlow/.

# Installation

## Get the bundle

Let Composer download and install the bundle by running

```sh
php composer.phar require craue/formflow-bundle:~3.0@dev
```

in a shell.

## Enable the bundle

```php
// in app/AppKernel.php
public function registerBundles() {
	$bundles = array(
		// ...
		new Craue\FormFlowBundle\CraueFormFlowBundle(),
	);
	// ...
}
```

# Note

To keep the amount of code in this documentation at a minimum and focus on the essentials, the examples target Symfony >= 2.8,
so if you use a lower version, some adjustments (especially to form types) may be required.

# Usage

This section shows how to create a 3-step form flow for creating a vehicle.
You have to choose between two approaches on how to set up your flow.

## Approach A: One form type for the entire flow

This approach makes it easy to turn an existing (common) form into a form flow.

### Create a flow class

```php
// src/MyCompany/MyBundle/Form/CreateVehicleFlow.php
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class CreateVehicleFlow extends FormFlow {

	protected function loadStepsConfig() {
		return array(
			array(
				'label' => 'wheels',
				'form_type' => 'MyCompany\MyBundle\Form\CreateVehicleForm',
			),
			array(
				'label' => 'engine',
				'form_type' => 'MyCompany\MyBundle\Form\CreateVehicleForm',
				'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
					return $estimatedCurrentStepNumber > 1 && !$flow->getFormData()->canHaveEngine();
				},
			),
			array(
				'label' => 'confirmation',
			),
		);
	}

}
```

### Create a form type class

You only have to create one form type class for a flow.
There is an option called `flow_step` you can use to decide which fields will be added to the form
according to the step to render.

```php
// src/MyCompany/MyBundle/Form/CreateVehicleForm.php
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateVehicleForm extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		switch ($options['flow_step']) {
			case 1:
				$validValues = array(2, 4);
				$builder->add('numberOfWheels', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
					'choices' => array_combine($validValues, $validValues),
					'placeholder' => '',
				));
				break;
			case 2:
				// This form type is not defined in the example.
				$builder->add('engine', 'MyCompany\MyBundle\Form\Type\VehicleEngineType', array(
					'placeholder' => '',
				));
				break;
		}
	}

	public function getBlockPrefix() {
		return 'createVehicle';
	}

}
```

## Approach B: One form type per step

This approach makes it easy to reuse the form types to compose other forms.

### Create a flow class

```php
// src/MyCompany/MyBundle/Form/CreateVehicleFlow.php
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class CreateVehicleFlow extends FormFlow {

	protected function loadStepsConfig() {
		return array(
			array(
				'label' => 'wheels',
				'form_type' => 'MyCompany\MyBundle\Form\CreateVehicleStep1Form',
			),
			array(
				'label' => 'engine',
				'form_type' => 'MyCompany\MyBundle\Form\CreateVehicleStep2Form',
				'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
					return $estimatedCurrentStepNumber > 1 && !$flow->getFormData()->canHaveEngine();
				},
			),
			array(
				'label' => 'confirmation',
			),
		);
	}

}
```

### Create form type classes

```php
// src/MyCompany/MyBundle/Form/CreateVehicleStep1Form.php
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateVehicleStep1Form extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$validValues = array(2, 4);
		$builder->add('numberOfWheels', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
			'choices' => array_combine($validValues, $validValues),
			'placeholder' => '',
		));
	}

	public function getBlockPrefix() {
		return 'createVehicleStep1';
	}

}
```

```php
// src/MyCompany/MyBundle/Form/CreateVehicleStep2Form.php
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateVehicleStep2Form extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('engine', 'MyCompany\MyBundle\Form\Type\VehicleEngineType', array(
			'placeholder' => '',
		));
	}

	public function getBlockPrefix() {
		return 'createVehicleStep2';
	}

}
```

## Register your flow as a service

XML
```xml
<services>
	<service id="myCompany.form.flow.createVehicle"
			class="MyCompany\MyBundle\Form\CreateVehicleFlow"
			parent="craue.form.flow">
	</service>
</services>
```

YAML
```yaml
services:
    myCompany.form.flow.createVehicle:
        class: MyCompany\MyBundle\Form\CreateVehicleFlow
        parent: craue.form.flow
```

## Create a form template

You only need one template for a flow.
The instance of your flow class is passed to the template in a variable called `flow` so you can use it to render the
form according to the current step.

```twig
{# in src/MyCompany/MyBundle/Resources/views/Vehicle/createVehicle.html.twig #}
<div>
	Steps:
	{% include '@CraueFormFlow/FormFlow/stepList.html.twig' %}
</div>
{{ form_start(form) }}
	{{ form_errors(form) }}

	{% if flow.getCurrentStepNumber() == 1 %}
		<div>
			When selecting four wheels you have to choose the engine in the next step.<br />
			{{ form_row(form.numberOfWheels) }}
		</div>
	{% endif %}

	{{ form_rest(form) }}

	{% include '@CraueFormFlow/FormFlow/buttons.html.twig' %}
{{ form_end(form) }}
```

Some CSS is needed to render the buttons correctly. The easiest way would be to let
[Assetic](https://symfony.com/doc/current/assetic/asset_management.html) load the provided file in your base template:

```twig
{% stylesheets '@CraueFormFlowBundle/Resources/assets/css/buttons.css' %}
	<link type="text/css" rel="stylesheet" href="{{ asset_url }}" />
{% endstylesheets %}
```

You can easily customize the default button look by using these variables to add one or more CSS classes to them:

- `craue_formflow_button_class_last` will apply either to the __next__ or __finish__ button
- `craue_formflow_button_class_finish` will specifically apply to the __finish__ button
- `craue_formflow_button_class_next` will specifically apply to the __next__ button
- `craue_formflow_button_class_back` will apply to the __back__ button
- `craue_formflow_button_class_reset` will apply to the __reset__ button

Example with Bootstrap button classes:

```twig
{% include '@CraueFormFlow/FormFlow/buttons.html.twig' with {
		craue_formflow_button_class_last: 'btn btn-primary',
		craue_formflow_button_class_back: 'btn',
		craue_formflow_button_class_reset: 'btn btn-warning',
	} %}
```

In the same manner you can customize the button labels:

- `craue_formflow_button_label_last` for either the __next__ or __finish__ button
- `craue_formflow_button_label_finish` for the __finish__ button
- `craue_formflow_button_label_next` for the __next__ button 
- `craue_formflow_button_label_back` for the __back__ button
- `craue_formflow_button_label_reset` for the __reset__ button

Example:

```twig
{% include '@CraueFormFlow/FormFlow/buttons.html.twig' with {
		craue_formflow_button_label_finish: 'submit',
		craue_formflow_button_label_reset: 'reset the flow',
	} %}
```

You can also remove the reset button by setting `craue_formflow_button_render_reset` to `false`.

## Create an action

```php
// in src/MyCompany/MyBundle/Controller/VehicleController.php
public function createVehicleAction() {
	$formData = new Vehicle(); // Your form data class. Has to be an object, won't work properly with an array.

	$flow = $this->get('myCompany.form.flow.createVehicle'); // must match the flow's service id
	$flow->bind($formData);

	// form of the current step
	$form = $flow->createForm();
	if ($flow->isValid($form)) {
		$flow->saveCurrentStepData($form);

		if ($flow->nextStep()) {
			// form for the next step
			$form = $flow->createForm();
		} else {
			// flow finished
			$em = $this->getDoctrine()->getManager();
			$em->persist($formData);
			$em->flush();

			$flow->reset(); // remove step data from the session

			return $this->redirect($this->generateUrl('home')); // redirect when done
		}
	}

	return $this->render('@MyCompanyMy/Vehicle/createVehicle.html.twig', array(
		'form' => $form->createView(),
		'flow' => $flow,
	));
}
```

# Explanations

## How the flow works

1. Dispatch `PreBindEvent`.
1. Dispatch `GetStepsEvent`.
1. Update the form data class with previously saved data of all steps. For each one, dispatch `PostBindSavedDataEvent`.
1. Evaluate which steps are skipped. Determine the current step.
1. Dispatch `PostBindFlowEvent`.
1. Create the form for the current step.
1. Bind the request to that form.
1. Dispatch `PostBindRequestEvent`.
1. Validate the form data.
1. Dispatch `PostValidateEvent`.
1. Save the form data.
1. Proceed to the next step.

## Method `loadStepsConfig`

The array returned by that method is used to create all steps of the flow.
The first item will be the first step. You can, however, explicitly index the array for easier readability.

Valid options per step are:
- `label` (`string`|`StepLabel`|`null`)
	- If you'd like to render an overview of all steps you have to set the `label` option for each step.
	- If using a callable on a `StepLabel` instance, it has to return a string value or `null`.
	- By default, the labels will be translated using the `messages` domain when rendered in Twig.
- `form_type` (`FormTypeInterface`|`string`|`null`)
	- The form type used to build the form for that step.
	- This value is passed to Symfony's form factory, thus the same rules apply as for creating common forms. If using a string, it has to be either the FQCN or the registered alias of the form type, depending on the version of Symfony your project is built with.
- `form_options` (`array`)
	- Options passed to the form type of that step.
- `skip` (`callable`|`bool`)
	- Decides whether the step will be skipped.
	- If using a callable...
		- it will receive the estimated current step number and the flow as arguments;
		- it has to return a boolean value;
		- it might be called more than once until the actual current step number has been determined.

### Examples

```php
protected function loadStepsConfig() {
	return array(
		array(
			'form_type' => 'MyCompany\MyBundle\Form\CreateVehicleStep1Form',
		),
		array(
			'form_type' => 'MyCompany\MyBundle\Form\CreateVehicleStep2Form',
			'skip' => true,
		),
		array(
		),
	);
}
```

```php
protected function loadStepsConfig() {
	return array(
		1 => array(
			'label' => 'wheels',
			'form_type' => 'MyCompany\MyBundle\Form\CreateVehicleStep1Form',
		),
		2 => array(
			'label' => StepLabel::createCallableLabel(function() { return 'engine'; })
			'form_type' => 'MyCompany\MyBundle\Form\CreateVehicleStep2Form',
			'form_options' => array(
				'validation_groups' => array('Default'),
			),
			'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
				return $estimatedCurrentStepNumber > 1 && !$flow->getFormData()->canHaveEngine();
			},
		),
		3 => array(
			'label' => 'confirmation',
		),
	);
}
```

# Advanced stuff

## Validation groups

To validate the form data class bound to the flow, a step-based validation group is passed to the form type.
By default, if the flow's `getName` method returns `createVehicle`, such a group is named `flow_createVehicle_step1`
for the first step. You can customize this name by setting the flow's property `validationGroupPrefix` explicitly.
The step number (1, 2, 3, etc.) will be appended by the flow.

Compared to standalone forms, setting the `validation_groups` option in your form type's `configureOptions`
method won't have any effect in the context of a flow. The value is just ignored, i.e. will be overwritten by the flow.
But there are other ways of defining custom validation groups:

- override the flow's `getFormOptions` method,
- use the `form_options` step option, or
- use the flow's `setGenericFormOptions` method.

The generated step-based validation group will be added by the flow, unless the `validation_groups` option is set to `false` or a closure.
In this case, it will **not** be added by the flow, so ensure the step forms are validated as expected.

## Disabling revalidation of previous steps

Take a look at [#98](https://github.com/craue/CraueFormFlowBundle/issues/98) for an example on why it's useful to
revalidate previous steps by default. But if you want (or need) to avoid revalidating previous steps, add this to your flow class:

```php
// in src/MyCompany/MyBundle/Form/CreateVehicleFlow.php
class CreateVehicleFlow extends FormFlow {

	protected $revalidatePreviousSteps = false;

	// ...

}
```

## Passing generic options to the form type

To set options common for the form type(s) of all steps you can use method `setGenericFormOptions`:

```php
// in src/MyCompany/MyBundle/Controller/VehicleController.php
public function createVehicleAction() {
	// ...
	$flow->setGenericFormOptions(array('action' => 'targetUrl'));
	$flow->bind($formData);
	$form = $flow->createForm();
	// ...
}
```

## Passing step-based options to the form type

To pass individual options to each step's form type you can use the step config option `form_options`:

```php
// in src/MyCompany/MyBundle/Form/CreateVehicleFlow.php
protected function loadStepsConfig() {
	return array(
		array(
			'label' => 'wheels',
			'form_type' => 'MyCompany\MyBundle\Form\CreateVehicleStep1Form',
			'form_options' => array(
				'validation_groups' => array('Default'),
			),
		),
	);
}
```

Alternatively, to set options based on previous steps (e.g. to render fields depending on submitted data) you can override method
`getFormOptions` of your flow class:

```php
// in src/MyCompany/MyBundle/Form/CreateVehicleFlow.php
public function getFormOptions($step, array $options = array()) {
	$options = parent::getFormOptions($step, $options);

	$formData = $this->getFormData();

	if ($step === 2) {
		$options['numberOfWheels'] = $formData->getNumberOfWheels();
	}

	return $options;
}
```

## Enabling dynamic step navigation

Dynamic step navigation means that the step list rendered will contain links to go back/forth to a specific step
(which has been done already) directly.
To enable it, add this to your flow class:

```php
// in src/MyCompany/MyBundle/Form/CreateVehicleFlow.php
class CreateVehicleFlow extends FormFlow {

	protected $allowDynamicStepNavigation = true;

	// ...

}
```

If you'd like to remove the parameters (added by using such a direct link) when submitting the form
you should modify the action for the opening form tag in the template like this:

```twig
{{ form_start(form, {'action': path(app.request.attributes.get('_route'),
		app.request.query.all | craue_removeDynamicStepNavigationParameters(flow))}) }}
```

## Handling of file uploads

File uploads are transparently handled by Base64-encoding the content and storing it in the session, so it may affect performance.
This feature is enabled by default for convenience, but can be disabled in the flow class as follows:

```php
// in src/MyCompany/MyBundle/Form/CreateVehicleFlow.php
class CreateVehicleFlow extends FormFlow {

	protected $handleFileUploads = false;

	// ...

}
```

By default, the system's directory for temporary files will be used for files restored from the session while loading step data.
You can set a custom one:

```php
// in src/MyCompany/MyBundle/Form/CreateVehicleFlow.php
class CreateVehicleFlow extends FormFlow {

	protected $handleFileUploadsTempDir = '/path/for/flow/uploads';

	// ...

}
```

## Enabling redirect after submit

This feature will allow performing a redirect after submitting a step to load the page containing the next step using a GET request.
To enable it, add this to your flow class:

```php
// in src/MyCompany/MyBundle/Form/CreateVehicleFlow.php
class CreateVehicleFlow extends FormFlow {

	protected $allowRedirectAfterSubmit = true;

	// ...

}
```

But you still have to perform the redirect yourself, so update your action like this:

```php
// in src/MyCompany/MyBundle/Controller/VehicleController.php
public function createVehicleAction() {
	// ...
	$flow->bind($formData);
	$form = $submittedForm = $flow->createForm();
	if ($flow->isValid($submittedForm)) {
		$flow->saveCurrentStepData($submittedForm);
		// ...
	}

	if ($flow->redirectAfterSubmit($submittedForm)) {
		$request = $this->getRequest();
		$params = $this->get('craue_formflow_util')->addRouteParameters(array_merge($request->query->all(),
				$request->attributes->get('_route_params')), $flow);

		return $this->redirect($this->generateUrl($request->attributes->get('_route'), $params));
	}

	// ...
	// return ...
}
```

## Using events

There are some events which you can subscribe to. Using all of them right inside your flow class could look like this:

```php
// in src/MyCompany/MyBundle/Form/CreateVehicleFlow.php
use Craue\FormFlowBundle\Event\GetStepsEvent;
use Craue\FormFlowBundle\Event\PostBindFlowEvent;
use Craue\FormFlowBundle\Event\PostBindRequestEvent;
use Craue\FormFlowBundle\Event\PostBindSavedDataEvent;
use Craue\FormFlowBundle\Event\PostValidateEvent;
use Craue\FormFlowBundle\Event\PreBindEvent;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreateVehicleFlow extends FormFlow implements EventSubscriberInterface {

	public function setEventDispatcher(EventDispatcherInterface $dispatcher) {
		parent::setEventDispatcher($dispatcher);
		$dispatcher->addSubscriber($this);
	}

	public static function getSubscribedEvents() {
		return array(
			FormFlowEvents::PRE_BIND => 'onPreBind',
			FormFlowEvents::GET_STEPS => 'onGetSteps',
			FormFlowEvents::POST_BIND_SAVED_DATA => 'onPostBindSavedData',
			FormFlowEvents::POST_BIND_FLOW => 'onPostBindFlow',
			FormFlowEvents::POST_BIND_REQUEST => 'onPostBindRequest',
			FormFlowEvents::POST_VALIDATE => 'onPostValidate',
		);
	}

	public function onPreBind(PreBindEvent $event) {
		// ...
	}

	public function onGetSteps(GetStepsEvent $event) {
		// ...
	}

	public function onPostBindSavedData(PostBindSavedDataEvent $event) {
		// ...
	}

	public function onPostBindFlow(PostBindFlowEvent $event) {
		// ...
	}

	public function onPostBindRequest(PostBindRequestEvent $event) {
		// ...
	}

	public function onPostValidate(PostValidateEvent $event) {
		// ...
	}

	// ...

}
```
