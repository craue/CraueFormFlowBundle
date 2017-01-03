<?php

namespace Craue\FormFlowBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Myakshin <koc-dp@yandex.ru>
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowFormExtension extends AbstractTypeExtension {

	/**
	 * {@inheritDoc}
	 */
	public function getExtendedType() {
		$useFqcn = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');

		return $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\FormType' : 'form';
	}

	/**
	 * {@inheritDoc}
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefined(array(
			'flow_instance',
			'flow_instance_key',
			'flow_step',
			'flow_step_key',
		));
	}

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$useFqcn = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
		$hiddenType = $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\HiddenType' : 'hidden';

		if (array_key_exists('flow_instance', $options) && array_key_exists('flow_instance_key', $options)) {
			$builder->add($options['flow_instance_key'], $hiddenType, array(
				'data' => $options['flow_instance'],
				'mapped' => false,
				'flow_instance_key' => $options['flow_instance_key'],
			));
		}

		if (array_key_exists('flow_step', $options) && array_key_exists('flow_step_key', $options)) {
			$builder->add($options['flow_step_key'], $hiddenType, array(
				'data' => $options['flow_step'],
				'mapped' => false,
				'flow_step_key' => $options['flow_step_key'],
			));
		}
	}

}
