<?php

namespace Craue\FormFlowBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FormFlowFormExtension extends AbstractTypeExtension {

		/**
		 * {@inheritdoc}
		 */
		public function getExtendedType() {
			return 'form';
		}

		/**
		 * {@inheritdoc}
		 */
		public function setDefaultOptions(OptionsResolverInterface $resolver) {
			$resolver->setOptional(array('flow_step', 'flow_step_key'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function buildForm(FormBuilderInterface $builder, array $options) {
			if (array_key_exists('flow_step', $options)) {
				$builder->add('flow_step', 'hidden', array('data' => $options['flow_step'], 'mapped' => false, 'flow_step_key' => $options['flow_step_key']));
			}
		}
}
