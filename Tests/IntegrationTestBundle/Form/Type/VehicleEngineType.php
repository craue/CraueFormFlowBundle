<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2013 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class VehicleEngineType extends AbstractType {

	/**
	 * {@inheritDoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$choices = array(
			'electric',
			'gas',
			'naturalGas',
		);
		$resolver->setDefaults(array(
			'choices' => array_combine($choices, $choices),
			'empty_value' => '',
		));
	}

	/**
	 * {@inheritDoc}
	 */
	public function getParent() {
		return 'choice';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'form_type_vehicleEngine';
	}

}
