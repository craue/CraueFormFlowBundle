<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CreateVehicleForm extends AbstractType {

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$useFqcn = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix'); // Symfony's Form component >=2.8
		$setChoicesAsValuesOption = $useFqcn && method_exists('Symfony\Component\Form\AbstractType', 'getName'); // Symfony's Form component >=2.8 && <3.0

		$defaultChoiceOptions = array();
		if ($setChoicesAsValuesOption) {
			$defaultChoiceOptions['choices_as_values'] = true;
		}

		switch ($options['flow_step']) {
			case 1:
				$choices = array(2, 4);
				$builder->add('numberOfWheels', $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\ChoiceType' : 'choice', array_merge($defaultChoiceOptions, array(
					'choices' => array_combine($choices, $choices),
					'placeholder' => '',
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
					'placeholder' => '',
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
