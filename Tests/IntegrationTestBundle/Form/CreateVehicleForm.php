<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2013 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CreateVehicleForm extends AbstractType {

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		switch ($options['flowStep']) {
			case 1:
				$choices = array(2, 4);
				$builder->add('numberOfWheels', 'choice', array(
					'choices' => array_combine($choices, $choices),
					'empty_value' => '',
				));
				break;
			case 2:
				$choices = array(
					'electric',
					'gas',
					'naturalGas',
				);
				$builder->add('engine', 'choice', array(
					'choices' => array_combine($choices, $choices),
					'empty_value' => '',
				));
				break;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'flowStep' => null,
		));
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'createVehicle';
	}

}
