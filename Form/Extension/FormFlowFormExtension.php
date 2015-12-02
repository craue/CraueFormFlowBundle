<?php

namespace Craue\FormFlowBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Konstantin Myakshin <koc-dp@yandex.ru>
 * @copyright 2011-2015 Christian Raue
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
		$optionNames = array(
			'flow_step',
			'flow_step_key',
		);

		if (method_exists($resolver, 'setDefined')) {
			$resolver->setDefined($optionNames);
		} else {
			$resolver->setOptional($optionNames); // for symfony/options-resolver < 2.6
		}
	}

	/**
	 * {@inheritDoc}
	 */
	// TODO remove as soon as Symfony >= 2.7 is required
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$this->configureOptions($resolver);
	}

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		if (array_key_exists('flow_step', $options) && array_key_exists('flow_step_key', $options)) {
			$useFqcn = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
			$builder->add($options['flow_step_key'], $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\HiddenType' : 'hidden', array(
				'data' => $options['flow_step'],
				'mapped' => false,
				'flow_step_key' => $options['flow_step_key'],
			));
		}
	}

}
