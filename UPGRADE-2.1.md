# Upgrade from 2.0.x to 2.1

This version comes with two changes which allow you to remove some superfluous code from your flows, form types, and templates:

  1. The step number is automatically added as option `flow_step` to forms. So you can remove all the code to manually
  set this option (which was called `flowStep` in the docs before) and just use the new option instead.
  2. The hidden step field is automatically added to the form. So you don't need to include the bundle's template for it.

## Flow

- In method `loadStepsConfig`, the `type` option doesn't need to be set on empty steps anymore just to avoid an exception.

	before:
	```php
	protected function loadStepsConfig() {
		return array(
			// ...
			array(
				'label' => 'confirmation',
				'type' => $this->formType, // needed to avoid InvalidOptionsException regarding option 'flowStep'
			),
		);
	}
	```

	after:
	```php
	protected function loadStepsConfig() {
		return array(
			// ...
			array(
				'label' => 'confirmation',
			),
		);
	}
	```

- In method `getFormOptions`, the `flowStep` option doesn't need to be set manually anymore.

	before:
	```php
	public function getFormOptions($step, array $options = array()) {
		$options = parent::getFormOptions($step, $options);

		$options['flowStep'] = $step;

		// ...

		return $options;
	}
	```

	after:
	```php
	public function getFormOptions($step, array $options = array()) {
		$options = parent::getFormOptions($step, $options);

		// ...

		return $options;
	}
	```

	If method `getFormOptions` was only overridden to set this option, it can be removed altogether.

## Form type

- In method `setDefaultOptions`, you don't have to set the `flowStep` option anymore.

	before:
	```php
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'flowStep' => null,
			// ...
		));
	}
	```

	after:
	```php
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			// ...
		));
	}
	```

	If method `setDefaultOptions` was only overridden to set this option, it can be removed altogether.

- In method `buildForm`, you can use the automatically set option `flow_step` now.

	before:
	```php
	public function buildForm(FormBuilderInterface $builder, array $options) {
		switch ($options['flowStep']) {
			// ...
		}
	}
	```

	after:
	```php
	public function buildForm(FormBuilderInterface $builder, array $options) {
		switch ($options['flow_step']) {
			// ...
		}
	}
	```

## Template

- Including the template `CraueFormFlowBundle:FormFlow:stepField.html.twig` is no longer needed.

	before:
	```twig
	<form method="post" {{ form_enctype(form) }}>
		{% include 'CraueFormFlowBundle:FormFlow:stepField.html.twig' %}
		{{ form_errors(form) }}
		{{ form_rest(form) }}
		{% include 'CraueFormFlowBundle:FormFlow:buttons.html.twig' %}
	</form>
	```

	after:
	```twig
	<form method="post" {{ form_enctype(form) }}>
		{{ form_errors(form) }}
		{{ form_rest(form) }}
		{% include 'CraueFormFlowBundle:FormFlow:buttons.html.twig' %}
	</form>
	```
