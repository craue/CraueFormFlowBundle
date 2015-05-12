# Upgrade from 1.x to 2.0

## Action

- Remove the form data as argument to method `createForm`.

	before:
	```php
	$form = $flow->createForm($formData);
	```

	after:
	```php
	$form = $flow->createForm();
	```

- Add the form as argument to method `saveCurrentStepData`.

	before:
	```php
	$flow->saveCurrentStepData();
	```

	after:
	```php
	$flow->saveCurrentStepData($form);
	```

## Events

- The current step number won't be determined by the time `PostBindSavedDataEvent` is dispatched. So `PostBindFlowEvent` has been added and should be used instead of `PostBindSavedDataEvent` for code which needs to access the current step number.

## Flow

- Add method `getName`. Let it return the same value `getName` does for your form type to continue working with the same validation groups.

	```php
	public function getName() {
		return 'createVehicle';
	}
	```

- Remove property `maxSteps` and method `loadStepDescriptions`. Replace them with methods `setFormType` and `loadStepsConfig`. Add option `flowStep` to method `getFormOptions.`

	before:
	```php
	protected $maxSteps = 3;

	protected function loadStepDescriptions() {
		return array(
			'wheels',
			'engine',
			'confirmation',
		);
	}
	```

	after:
	```php
	use Symfony\Component\Form\FormTypeInterface;

	/**
	 * @var FormTypeInterface
	 */
	protected $formType;

	public function setFormType(FormTypeInterface $formType) {
		$this->formType = $formType;
	}

	protected function loadStepsConfig() {
		return array(
			array(
				'label' => 'wheels',
				'type' => $this->formType,
			),
			array(
				'label' => 'engine',
				'type' => $this->formType,
			),
			array(
				'label' => 'confirmation',
				'type' => $this->formType,
			),
		);
	}

	public function getFormOptions($step, array $options = array()) {
		$options = parent::getFormOptions($step, $options);

		$options['flowStep'] = $step;

		return $options;
	}
	```

- Method `getFormOptions` doesn't receive the form data as an argument anymore. You have to get it if needed. Also, step-based options don't necessarily also need to be available for all subsequent steps anymore.

	before:
	```php
	public function getFormOptions($formData, $step, array $options = array()) {
		$options = parent::getFormOptions($formData, $step, $options);

		if ($step > 1) {
			$options['numberOfWheels'] = $formData->getNumberOfWheels();
		}

		return $options;
	}
	```

	after:
	```php
	public function getFormOptions($step, array $options = array()) {
		$options = parent::getFormOptions($step, $options);

		$formData = $this->getFormData();

		if ($step === 2) { // if you need this option only for step 2
			$options['numberOfWheels'] = $formData->getNumberOfWheels();
		}

		return $options;
	}
	```

- Some methods have been renamed to make more clear what they do.

	- `getMaxSteps` to `getStepCount`
	- `getCurrentStep` to `getCurrentStepNumber`
	- `getCurrentStepDescription` to `getCurrentStepLabel`
	- `getStepDescriptions` to `getStepLabels`
	- `getFirstStep` to `getFirstStepNumber`
	- `getLastStep` to `getLastStepNumber`
	- `hasSkipStep` to `isStepSkipped`
	- `getRequestedStep` to `getRequestedStepNumber`
	- `determineCurrentStep` to `determineCurrentStepNumber`

- One method has been made protected.

	- `applySkipping`

- Some methods' signatures have changed in several ways.

	- `public function createForm($formData, array $options = array())` to `public function createForm(array $options = array())`
	- `public function getFormOptions($formData, $step, array $options = array())` to `public function getFormOptions($step, array $options = array())`
	- `public function determineCurrentStep()` to `protected function determineCurrentStepNumber()`
	- `public function getRequestedStep()` to `protected function getRequestedStepNumber()`
	- `protected function createFormForStep($formData, $step, array $options = array())` to `protected function createFormForStep($stepNumber, array $options = array())`
	- `public function saveCurrentStepData()` to `public function saveCurrentStepData(FormInterface $form)`
	- `public function applyDataFromSavedSteps($formData, array $options = array())` to `protected function applyDataFromSavedSteps()`

- Some methods have been removed.

	- `setFormType`/`getFormType`
	- `setMaxSteps`
	- `setCurrentStep`
	- `addSkipStep`/`removeSkipStep`
	- `loadStepDescriptions`

- Some properties have been renamed and/or made private. Use their public accessors instead.

	- `id`: `setId`/`getId`
	- `formStepKey`: `setFormStepKey`/`getFormStepKey`
	- `formTransitionKey`: `setFormTransitionKey`/`getFormTransitionKey`
	- `stepDataKey`: `setStepDataKey`/`getStepDataKey`
	- `validationGroupPrefix`: `setValidationGroupPrefix`/`getValidationGroupPrefix`
	- `maxSteps`: `getStepCount`
	- `stepDescriptions`: `getStepLabels`
	- `currentStep`: `getCurrentStepNumber`
	- `request`: `getRequest`

- Some properties have been removed.

	- `formType`
	- `skipSteps`

- After calling `nextStep`, now the method `getCurrentStepNumber` won't return a value greater than what `getStepCount` returns. This used to be different in 1.x, where `getCurrentStep` returned `getMaxSteps() + 1` in case the flow is finished. 

## Template

- Block `craue_flow_stepDescription` has been renamed to `craue_flow_stepLabel` and the variable it accesses has been renamed from `stepDescription` to `stepLabel`.

	before:
	```twig
	{{ block('craue_flow_stepDescription') }}
	```

	after:
	```twig
	{{ block('craue_flow_stepLabel') }}
	```

	before:
	```twig
	{% block craue_flow_stepDescription %}
		<span>{{ stepDescription | trans }}</span>
	{% endblock %}
	```

	after:
	```twig
	{% block craue_flow_stepLabel %}
		<span>{{ stepLabel | trans }}</span>
	{% endblock %}
	```
