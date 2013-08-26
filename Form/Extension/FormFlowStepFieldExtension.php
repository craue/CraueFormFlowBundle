<?php

namespace Craue\FormFlowBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Konstantin Myakshin <koc-dp@yandex.ru>
 * @copyright 2011-2013 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowStepFieldExtension extends AbstractTypeExtension {

	/**
	 * {@inheritDoc}
	 */
	public function getExtendedType() {
		return 'hidden';
	}

	/**
	 * {@inheritDoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setOptional(array('flow_step_key'));
	}

	/**
	 * {@inheritDoc}
	 */
	public function finishView(FormView $view, FormInterface $form, array $options) {
		if (isset($options['flow_step_key']) && $view->vars['name'] === $options['flow_step_key']) {
			$view->vars['value'] = $options['data'];
			$view->vars['full_name'] = $options['flow_step_key'];
		}
	}
}
