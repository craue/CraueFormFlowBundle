<?php

namespace Craue\FormFlowBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Konstantin Myakshin <koc-dp@yandex.ru>
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowHiddenFieldExtension extends AbstractTypeExtension {

	/**
	 * {@inheritDoc}
	 */
	public function getExtendedType() {
		return 'hidden';
	}

	/**
	 * {@inheritDoc}
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$optionNames = array(
			'flow_instance_key',
			'flow_step_key',
		);

		if (Kernel::VERSION_ID < 20600) {
			$resolver->setOptional($optionNames);
		} else {
			$resolver->setDefined($optionNames);
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
	public function finishView(FormView $view, FormInterface $form, array $options) {
		if (array_key_exists('flow_instance_key', $options) && $view->vars['name'] === $options['flow_instance_key']) {
			$view->vars['value'] = $options['data'];
			$view->vars['full_name'] = $options['flow_instance_key'];
		}

		if (array_key_exists('flow_step_key', $options) && $view->vars['name'] === $options['flow_step_key']) {
			$view->vars['value'] = $options['data'];
			$view->vars['full_name'] = $options['flow_step_key'];
		}
	}

}
