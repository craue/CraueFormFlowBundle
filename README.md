# Information

CraueFormFlowBundle provides a facility for building and handling multi-step forms.
It makes it easy to turn an existing form into a multi-step form flow.

Features:

- navigation (next, back, start over)
- step descriptions
- skipping of specified steps
- different validation group for each step
- dynamic step navigation

A live demo can be found at http://craue.de/sf2playground/en/CraueFormFlow/.

This bundle should be used in conjunction with Symfony2.

# Installation

Please use tag 1.0.0 of this bundle if you need Symfony 2.0.x compatibility.

## Get the bundle

Let Composer download and install the bundle by first adding it to your composer.json

```json
{
	"require": {
		"craue/formflow-bundle": "~1.1.0"
	}
}
```

and then running

```sh
php composer.phar update craue/formflow-bundle
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

# Usage

This section shows how to create a 3-step form flow for user registration.

## Create a flow class

```php
// src/MyCompany/MyBundle/Form/RegisterUserFlow.php
use Craue\FormFlowBundle\Form\FormFlow;

class RegisterUserFlow extends FormFlow {

	protected $maxSteps = 3;

}
```

If you'd like to render an overview of all steps you have to implement a `loadStepDescriptions` method returning an
array of descriptions where the value with index 0 will be the description for step 1:

```php
// in src/MyCompany/MyBundle/Form/RegisterUserFlow.php
protected function loadStepDescriptions() {
	return array(
		'Account',
		'Password',
		'Terms of service',
	);
}
```

By default, these descriptions will be translated using the `messages` domain when rendered in Twig.

## Create a form type class

You only have to create one form type class for a flow.
An option called `flowStep` is passed to the form type so it can build the form according to the step to render.

```php
// src/MyCompany/MyBundle/Form/RegisterUserFormType.php
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegisterUserFormType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		switch ($options['flowStep']) {
			case 1:
				$builder->add('username');
				$builder->add('email', 'email');
				break;
			case 2:
				$builder->add('plainPassword', 'repeated', array(
					'type' => 'password',
				));
				break;
			case 3:
				$builder->add('termsOfService', 'checkbox');
				break;
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'flowStep' => 1,
			'data_class' => 'MyCompany\MyBundle\Entity\MyUser', // should point to your user entity
		));
	}

	public function getName() {
		return 'registerUser';
	}

}
```

## Register your form type and flow as services

```xml
<services>
	<service id="myCompany.form.registerUser"
			class="MyCompany\MyBundle\Form\RegisterUserFormType">
		<tag name="form.type" alias="registerUser" />
	</service>

	<service id="myCompany.form.flow.registerUser"
			class="MyCompany\MyBundle\Form\RegisterUserFlow"
			parent="craue.form.flow">
		<call method="setFormType">
			<argument type="service" id="myCompany.form.registerUser" />
		</call>
	</service>
</services>
```

## Create a form template

You also only need one template for a flow.
The instance of your flow class is passed to the template in a variable called `flow` so you can use it to render the
form according to the current step.

```html+jinja
{# in src/MyCompany/MyBundle/Resources/views/User/registerUser.html.twig #}
<div>
	Steps:
	{% include 'CraueFormFlowBundle:FormFlow:stepList.html.twig' %}
</div>
<form method="post" {{ form_enctype(form) }}>
	{% include 'CraueFormFlowBundle:FormFlow:stepField.html.twig' %}

	{{ form_errors(form) }}

	{% if flow.getCurrentStep() == 3 %}
		<div>
			You have to agree to the terms of service to register.<br />
			{{ form_row(form.termsOfService) }}
		</div>
	{% endif %}

	{{ form_rest(form) }}

	{% include 'CraueFormFlowBundle:FormFlow:buttons.html.twig' %}
</form>
```

For the buttons to render correctly you need to tell Assetic to include a CSS file.
So place this in your base template:

```html+jinja
{% stylesheets '@CraueFormFlowBundle/Resources/assets/css/buttons.css' %}
	<link type="text/css" rel="stylesheet" href="{{ asset_url }}" />
{% endstylesheets %}
```

## Create an action

```php
// in src/MyCompany/MyBundle/Controller/UserController.php
/**
 * @Template
 */
public function registerUserAction() {
	$user = new MyUser(); // Should be your user entity. Has to be an object, won't work properly with an array.

	$flow = $this->get('myCompany.form.flow.registerUser'); // must match the flow's service id
	$flow->bind($user);

	// form of the current step
	$form = $flow->createForm($user);
	if ($flow->isValid($form)) {
		$flow->saveCurrentStepData();

		if ($flow->nextStep()) {
			// form for the next step
			$form = $flow->createForm($user);
		} else {
			// flow finished
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($user);
			$em->flush();

			return $this->redirect($this->generateUrl('home')); // redirect when done
		}
	}

	return array(
		'form' => $form->createView(),
		'flow' => $flow,
	);
}
```

# Advanced stuff

## Validation groups

To validate the form data class a step-based validation group is passed to the form type.
By default, if `getName()` of the form type returns `registerUser`, such a group is named `flow_registerUser_step1`
for the first step.

## Passing step-based options to the form type

If your form type needs options to build the form (e.g. conditional fields) you can override the `getFormOptions`
method of your flow class.
Before you can use the options you have to define them in your form type class:

```php
// in src/MyCompany/MyBundle/Form/RegisterUserFormType.php
public function setDefaultOptions(OptionsResolverInterface $resolver) {
	$resolver->setDefaults(array(
		// ...
		'givenUsername' => null,
	));
}
```

Then you can set them in your flow class.
It's important that an option needed for one step is also available for all subsequent ones, so don't use `switch`
here.

```php
// in src/MyCompany/MyBundle/Form/RegisterUserFlow.php
public function getFormOptions($formData, $step, array $options = array()) {
	$options = parent::getFormOptions($formData, $step, $options);

	if ($step > 1) {
		$options['givenUsername'] = $formData->getUsername();
	}

	return $options;
}
```

## Enabling dynamic step navigation

Dynamic step navigation means that the step list rendered will contain links to go back/forth to a specific step
(which has been done already) directly.
To enable it you could extend the flow class mentioned in the example above as follows:

```php
// in src/MyCompany/MyBundle/Form/RegisterUserFlow.php
class RegisterUserFlow extends FormFlow {

	protected $allowDynamicStepNavigation = true;

	// ...

}
```

To force clearing of saved step data when finishing the flow you should call `$flow->reset()` in the action:

```php
// in src/MyCompany/MyBundle/Controller/UserController.php
public function registerUserAction() {
	// ...

	// flow finished
	// ...
	$flow->reset();

	// ...
}
```

To ensure starting a flow with clean data, it would be a good idea to add a separate action as an entry point which
just resets the flow and redirects to the usual action:

```php
// in src/MyCompany/MyBundle/Controller/UserController.php
public function registerUserStartAction() {
	// ...

	$flow = $this->get('myCompany.form.flow.registerUser');
	$flow->reset();

	return $this->redirect($this->generateUrl('...')); // route name for registerUserAction
}
```

Furthermore, if you'd like to remove the step parameter (added by using such a direct link) when submitting the form
you should modify the opening form tag in the form template like this:

```html+jinja
<form method="post" action="{{ path(app.request.attributes.get('_route'),
		app.request.query.all | craue_removeDynamicStepNavigationParameter(flow)) }}" {{ form_enctype(form) }}>
```

## Using events

There are some events which you can subscribe to. Using all of them right inside your flow class could look like this:

```php
// in src/MyCompany/MyBundle/Form/RegisterUserFlow.php
use Craue\FormFlowBundle\Event\PostBindRequestEvent;
use Craue\FormFlowBundle\Event\PostBindSavedDataEvent;
use Craue\FormFlowBundle\Event\PostValidateEvent;
use Craue\FormFlowBundle\Event\PreBindEvent;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegisterUserFlow extends FormFlow implements EventSubscriberInterface {

	public function setEventDispatcher(EventDispatcherInterface $dispatcher) {
		parent::setEventDispatcher($dispatcher);
		$dispatcher->addSubscriber($this);
	}

	public static function getSubscribedEvents() {
		return array(
			FormFlowEvents::PRE_BIND => 'onPreBind',
			FormFlowEvents::POST_BIND_SAVED_DATA => 'onPostBindSavedData',
			FormFlowEvents::POST_BIND_REQUEST => 'onPostBindRequest',
			FormFlowEvents::POST_VALIDATE => 'onPostValidate',
		);
	}

	public function onPreBind(PreBindEvent $event) {
		// ...
	}

	public function onPostBindSavedData(PostBindSavedDataEvent $event) {
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
