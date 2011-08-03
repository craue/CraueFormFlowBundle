# Information

CraueFormFlowBundle provides a facility for building and handling multi-step forms.
It supports navigation (next, back, start over) within the single steps and makes it easy to turn an existing form into
a multi-step form flow.

This bundle should be used in conjunction with Symfony2.

# Installation

## Add CraueFormFlowBundle to your vendor directory

Either by using a Git submodule:

	git submodule add https://github.com/craue/CraueFormFlowBundle.git vendor/bundles/Craue/FormFlowBundle

Or by using the `deps` file:

	[CraueFormFlowBundle]
	    git=https://github.com/craue/CraueFormFlowBundle.git
	    target=bundles/Craue/FormFlowBundle

## Add CraueFormFlowBundle to your application kernel

	// app/AppKernel.php
	public function registerBundles() {
		$bundles = array(
			// ...
			new Craue\FormFlowBundle\CraueFormFlowBundle(),
		);
		// ...
	}

## Register the Craue namespace

	// app/autoload.php
	$loader->registerNamespaces(array(
		// ...
		'Craue' => __DIR__.'/../vendor/bundles',
	));

# Usage

This sections shows how to create a 3-step form flow for user registration.

## Create a flow class

	// src/MyCompany/MyBundle/Form/RegisterUserFlow.php
	use Craue\FormFlowBundle\Form\FormFlow;

	class RegisterUserFlow extends FormFlow {

		protected $maxSteps = 3;

	}

## Create a form type class

You only have to create one form type class for a flow.
An option called `flowStep` is passed to the form type so it can build the form according to the step to render.

	// src/MyCompany/MyBundle/Form/RegisterUserFormType.php
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\FormBuilder;

	class RegisterUserFormType extends AbstractType {

		public function buildForm(FormBuilder $builder, array $options) {
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

		public function getDefaultOptions(array $options) {
			$options = parent::getDefaultOptions($options);

			$options['flowStep'] = 1;
			$options['data_class'] = 'MyCompany\MyBundle\Entity\MyUser'; // should point to your user entity

			return $options;
		}

		public function getName() {
			return 'registerUser';
		}

	}

## Register your form type and flow as services

	<services>
		<service id="myCompany.form.registerUser"
				class="MyCompany\MyBundle\Form\RegisterUserFormType">
			<tag name="form.type" alias="registerUser" />
		</service>

		<service id="myCompany.form.flow.registerUser"
				class="MyCompany\MyBundle\Form\RegisterUserFlow"
				scope="request">
			<call method="setFormType">
				<argument type="service" id="myCompany.form.registerUser" />
			</call>
			<call method="setFormFactory">
				<argument type="service" id="form.factory" />
			</call>
			<call method="setRequest">
				<argument type="service" id="request" />
			</call>
			<call method="setSession">
				<argument type="service" id="session" />
			</call>
		</service>
	</services>

## Create a form template

You also only need one template for a flow. 
The instance of your flow class is passed to the template in a variable called `flow` so you can use it to render the
form according to the current step.

	// in src/MyCompany/MyBundle/Resources/views/User/registerUser.html.twig
	<form method="post" {{ form_enctype(form) }}>
		{% include 'CraueFormFlowBundle:FormFlow:stepField.html.twig' %}

		{{ form_errors(form) }}

		{% if flow.getCurrentStep() == 3 %}
			<div>
				You have to agree to the terms of service to register.<br/>
				{{ form_row(form.termsOfService) }}
			</div>
		{% endif %}

		{{ form_rest(form) }}

		{% include 'CraueFormFlowBundle:FormFlow:buttons.html.twig' %}
	</form>

For the buttons to render correctly you need to tell Assetic to include a CSS file.
So place this in your base template:

	{% stylesheets '@CraueFormFlowBundle/Resources/assets/css/buttons.css' %}
		<link type="text/css" rel="stylesheet" href="{{ asset_url }}" />
	{% endstylesheets %}

## Create an action

	// in src/MyCompany/MyBundle/Controller/UserController.php
	/**
	 * @Template
	 */
	public function registerUserAction() {
		$user = new MyUser(); // should be your user entity

		$flow = $this->get('myCompany.form.flow.registerUser'); // must match the flow's service id
		$flow->bind($user);

		$form = $flow->createForm($user);
		if ($flow->isValid($form)) {
			$flow->saveCurrentStepData();

			if ($flow->getCurrentStep() < $flow->getMaxSteps()) {
				$flow->nextStep();
				// render form for next step
				return array(
					'form' => $flow->createForm($user)->createView(),
					'flow' => $flow,
				);
			}

			// flow finished
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($user);
			$em->flush();

			return $this->redirect($this->generateUrl('home'));
		}

		return array(
			'form' => $form->createView(),
			'flow' => $flow,
		);
	}

# Advanced stuff

## Validation groups

To validate the form data class a step-based validation group is passed to the form type.
By default, if `getName()` of the form type returns `registerUser`, such a group is named `flow_registerUser_step1`
for the first step.

## Passing step-based options to the form type

If your form type needs options to build the form you can override the `getFormOptions` method of the flow class.
It's important that an option needed for one step is also available for all subsequent ones, so don't use `switch`
here.

	// in src/MyCompany/MyBundle/Form/RegisterUserFlow.php
	public function getFormOptions($formData, $step, array $options = array()) {
		$options = parent::getFormOptions($formData, $step, $options);

		if ($step > 1) {
			$options['givenUsername'] = $formData->getUsername();
		}

		return $options;
	}
