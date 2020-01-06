<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CreateVehicleForm extends AbstractType {

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		switch ($options['flow_step']) {
			case 1:
				$choices = [2, 4];
				$builder->add('numberOfWheels', ChoiceType::class, [
					'choices' => array_combine($choices, $choices),
					'placeholder' => '',
				]);
				break;
			case 2:
				$choices = [
					'electric',
					'gas',
					'naturalGas',
				];
				$builder->add('engine', ChoiceType::class, [
					'choices' => array_combine($choices, $choices),
					'placeholder' => '',
				]);
				break;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBlockPrefix() {
		return 'createVehicle';
	}

}
