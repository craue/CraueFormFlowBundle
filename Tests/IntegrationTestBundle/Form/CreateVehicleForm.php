<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CreateVehicleForm extends AbstractType {

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$useFqcn = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix'); // Symfony's Form component >=2.8
		$usePlaceholder = method_exists('Symfony\Component\Form\AbstractType', 'configureOptions'); // Symfony's Form component 2.6 deprecated the "empty_value" option, but there seems to be no way to detect that version, so stick to this >=2.7 check.

		$defaultChoiceOptions = array();
		if ($useFqcn) {
			$defaultChoiceOptions['choices_as_values'] = true;
		}

		switch ($options['flow_step']) {
			case 1:
				$choices = array(2, 4);
				$builder->add('numberOfWheels', $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\ChoiceType' : 'choice', array_merge($defaultChoiceOptions, array(
					'choices' => array_combine($choices, $choices),
					$usePlaceholder ? 'placeholder' : 'empty_value' => '',
				)));
				break;
			case 2:
				$choices = array(
					'electric',
					'gas',
					'naturalGas',
				);
				$builder->add('engine', $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\ChoiceType' : 'choice', array_merge($defaultChoiceOptions, array(
					'choices' => array_combine($choices, $choices),
					$usePlaceholder ? 'placeholder' : 'empty_value' => '',
				)));
				break;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return $this->getBlockPrefix();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBlockPrefix() {
		return 'createVehicle';
	}

}
