# Upgrade from 2.1.x to 3.0

## Removal of request scope from service definitions

- To ensure compatibility with the latest versions of Symfony, the request scope has been removed from all service
  definitions. You in turn also have to remove the scope from your flows and connected form types.

	before:
	```xml
	<service id="myCompany.form.flow.createTopic"
			class="MyCompany\MyBundle\Form\CreateTopicFlow"
			parent="craue.form.flow"
			scope="request">
	</service>
	```

	after:
	```xml
	<service id="myCompany.form.flow.createTopic"
			class="MyCompany\MyBundle\Form\CreateTopicFlow"
			parent="craue.form.flow">
	</service>
	```

## Renaming step config option `type` to `form_type`

- The step config option to specify the form type for each step within the `loadStepsConfig` method has been renamed
  from `type` to `form_type`. This was done for the sake of consistency with the newly added option `form_options`.
  The old option `type` is still available, but deprecated.

	before:
	```php
	protected function loadStepsConfig() {
		return array(
			array(
				'type' => $this->formType,
			),
			// ...
		);
	}
	```

	after:
	```php
	protected function loadStepsConfig() {
		return array(
			array(
				'form_type' => $this->formType,
			),
			// ...
		);
	}
	```

## Concurrent instances of the same flow

- This version adds support for concurrent instances of the same flow, which required a change in the handling of flows.

- When performing a GET request _without any additional parameters_ to run a flow with dynamic step navigation enabled,
  it has just been reused as there could only be one instance using the default session storage. So previously, the
  data of all steps would still be available. Now, a new flow instance will be started. Thus, if you want to provide a
  custom link to the same flow instance, (beside the optional step number) you now need to add the instance id as
  parameter `instance` (per default).

	before:
	```twig
	<a href="{{ path('createTopic', {'step': 2}) }}">continue creating a topic</a>
	```

	after:
	```twig
	<a href="{{ path('createTopic', {'instance': flow.getInstanceId(), 'step': 2}) }}">continue creating a topic</a>
	```

- For the same reason, it's no longer necessary to use a dedicated action to reset a flow in order to start it with
  clean data.

	before:
	```php
	/**
	 * @Route("/create-topic/start/", name="createTopic_start")
	 */
	public function createTopicStartAction() {
		$flow = $this->get('form.flow.createTopic');
		$flow->reset();

		return $this->redirect($this->generateUrl('createTopic'));
	}
	```
	```twig
	<a href="{{ path('createTopic_start') }}">create a topic</a>
	```

	after:
	```twig
	<a href="{{ path('createTopic') }}">create a topic</a>
	```

- To remove saved step data from the session when finishing the flow you should call `$flow->reset()` at the end of the
  action.

	before:
	```php
	public function createTopicAction() {
		// ...

		// flow finished
		// persist data to the DB or whatever...

		// redirect when done...
	}
	```

	after:
	```php
	public function createTopicAction() {
		// ...

		// flow finished
		// persist data to the DB or whatever...

		$flow->reset();

		// redirect when done...
	}
	```

## Removal of options from method `createForm`

- Options cannot be passed to step forms using `createForm` anymore. You can now use `setGenericFormOptions` for that.

	before:
	```php
	$flow->bind($formData);
	$form = $flow->createForm(array('action' => 'targetUrl'));
	```

	after:
	```php
	$flow->setGenericFormOptions(array('action' => 'targetUrl'));
	$flow->bind($formData);
	$form = $flow->createForm();
	```

## Events

- Some methods have been renamed.

	- `PostBindRequestEvent`: `getStep` to `getStepNumber`
	- `PostBindSavedDataEvent`: `getStep` to `getStepNumber`

- Some properties have been renamed.

	- `PostBindRequestEvent`: `step` to `stepNumber`
	- `PostBindSavedDataEvent`: `step` to `stepNumber`

## Flow

- A default implementation for method `getName` has been added. If you just let it return the class name with the first
  letter lower-cased and without the "Flow" suffix, you can remove it from your flow since the default implementation
  will return the same value.

	before:
	```php
	class CreateVehicleFlow extends FormFlow {

		public function getName() {
			return 'createVehicle';
		}

		// ...
	}
	```

	after:
	```php
	class CreateVehicleFlow extends FormFlow {
		// ...
	}
	```

- The signature of method `setRequest` has changed to accept a `RequestStack` instance.

	- `public function setRequest(Request $request = null)` to `public function setRequestStack(RequestStack $requestStack)`

- Some methods have been removed.

	- `setStepDataKey`/`getStepDataKey`
	- `setStorage`/`getStorage` (call `getDataManager()->getStorage()` instead or adapt your code to use
	  `setDataManager`/`getDataManager`)

- Some methods have been renamed.

	- `setDynamicStepNavigationParameter` to `setDynamicStepNavigationStepParameter`
	- `getDynamicStepNavigationParameter` to `getDynamicStepNavigationStepParameter`

- A property has been removed.

	- `storage` (call `$this->dataManager->getStorage()` instead)

- A property has been renamed.

	- `dynamicStepNavigationParameter` to `dynamicStepNavigationStepParameter`

## Step

- Some methods have been renamed.

	- `setType` to `setFormType`
	- `getType` to `getFormType`

- A property has been renamed.

	- `type` to `formType`

## Storage

- The signature of method `remove` in `StorageInterface` has changed to not return the removed value anymore.

## Template

- The Twig filters `craue_addDynamicStepNavigationParameter` and `craue_removeDynamicStepNavigationParameter` have been
  renamed to `craue_addDynamicStepNavigationParameters` and `craue_removeDynamicStepNavigationParameters`, i.e.
  pluralized, since they now handle more than one parameter. Filters with the old names still exist, but are deprecated.

- The template `CraueFormFlowBundle:FormFlow:stepField.html.twig` (deprecated in 2.1.0) has been removed.
