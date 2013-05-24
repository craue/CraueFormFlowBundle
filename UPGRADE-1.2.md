# Upgrade from 1.1.x to 1.2

## Flow

- The basic service `craue.form.flow` is not in request scope anymore. So your flows inheriting this one also need to be taken out of request scope.

	before:
	```xml
	<service id="integrationTestBundle.form.flow.createVehicle"
			class="Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\CreateVehicleFlow"
			parent="craue.form.flow"
			scope="request">
		<call method="setFormType">
			<argument type="service" id="integrationTestBundle.form.createVehicle" />
		</call>
	</service>
	```

	after:
	```xml
	<service id="integrationTestBundle.form.flow.createVehicle"
			class="Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\CreateVehicleFlow"
			parent="craue.form.flow">
		<call method="setFormType">
			<argument type="service" id="integrationTestBundle.form.createVehicle" />
		</call>
	</service>
	```

- The protected property `request` has been removed. Use the public method `getRequest` instead.
