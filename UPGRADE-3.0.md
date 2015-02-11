# Upgrade from 2.1.x to 3.0

## Concurrent instances of the same flow

This version adds support for concurrent instances of the same flow, which required a change in the handling of flows.

- When performing a GET request _without any additional parameters_ to run a flow with dynamic step navigation enabled,
  it has just been reused as there could only be one instance using the default session storage. So previously, the
  data of all steps would still be available. Now, a new flow instance will be started. Thus, if you want to provide a
  custom link to the same flow instance, (beside the optional step number) you now need to add the instance id as
  parameter `instance` (per default).

	before:
	```html+jinja
	<a href="{{ path('createTopic', {'step': 2}) }}">continue creating a topic</a>
	```

	after:
	```html+jinja
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
	```html+jinja
	<a href="{{ path('createTopic_start') }}">create a topic</a>
	```

	after:
	```html+jinja
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
	$flow->bind($formData);
	$flow->setGenericFormOptions(array('action' => 'targetUrl'));
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

- Some methods have been removed.

	- `setStepDataKey`/`getStepDataKey`
	- `setStorage`/`getStorage` (use `setDataManager`/`getDataManager` instead)

- Some methods have been renamed.

	- `setDynamicStepNavigationParameter` to `setDynamicStepNavigationStepParameter`
	- `getDynamicStepNavigationParameter` to `getDynamicStepNavigationStepParameter`

- A property has been renamed.

	- `dynamicStepNavigationParameter` to `dynamicStepNavigationStepParameter`

## Template

- The Twig filters `craue_addDynamicStepNavigationParameter` and `craue_removeDynamicStepNavigationParameter` have been
  renamed to `craue_addDynamicStepNavigationParameters` and `craue_removeDynamicStepNavigationParameters`, i.e.
  pluralized, since they now handle more than one parameter.

- The template `CraueFormFlowBundle:FormFlow:stepField.html.twig` (deprecated in 2.1.0) has been removed.
