<?php

namespace Craue\FormFlowBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Myakshin <koc-dp@yandex.ru>
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2022 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowFormExtension extends AbstractTypeExtension {

	/**
	 * {@inheritDoc}
	 */
	public function getExtendedType() {
		return FormType::class;
	}

	public static function getExtendedTypes() : iterable {
		return [FormType::class];
	}

	/**
	 * {@inheritDoc}
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefined([
			'flow_instance',
			'flow_instance_key',
			'flow_step',
			'flow_step_key',
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		if (array_key_exists('flow_instance', $options) && array_key_exists('flow_instance_key', $options)) {
			$builder->add($options['flow_instance_key'], HiddenType::class, [
				'data' => $options['flow_instance'],
				'mapped' => false,
				'flow_instance_key' => $options['flow_instance_key'],
			]);
		}

		if (array_key_exists('flow_step', $options) && array_key_exists('flow_step_key', $options)) {
			$builder->add($options['flow_step_key'], HiddenType::class, [
				'data' => $options['flow_step'],
				'mapped' => false,
				'flow_step_key' => $options['flow_step_key'],
			]);
		}
	}

}
