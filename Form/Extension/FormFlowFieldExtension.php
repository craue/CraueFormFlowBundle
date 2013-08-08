<?php

namespace Craue\FormFlowBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FormFlowFieldExtension extends AbstractTypeExtension {

		/**
		 * {@inheritdoc}
		 */
		public function getExtendedType() {
			return 'hiden';
		}

		/**
		 * {@inheritdoc}
		 */
		public function setDefaultOptions(OptionsResolverInterface $resolver) {
			$resolver->setOptional(array('flow_step_key'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function finishView(FormView $view, FormInterface $form, array $options) {
			if ('flow_step' === $view->vars['name']) {
				$view->vars['full_name'] = $options['flow_step_key'];
			}
		}
}
