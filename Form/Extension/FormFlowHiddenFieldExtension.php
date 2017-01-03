<?php

namespace Craue\FormFlowBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Myakshin <koc-dp@yandex.ru>
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowHiddenFieldExtension extends AbstractTypeExtension {

	/**
	 * {@inheritDoc}
	 */
	public function getExtendedType() {
		$useFqcn = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');

		return $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\HiddenType' : 'hidden';
	}

	/**
	 * {@inheritDoc}
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefined(array(
			'flow_instance_key',
			'flow_step_key',
		));
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
