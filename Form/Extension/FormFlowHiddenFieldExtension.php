<?php

namespace Craue\FormFlowBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @internal
 */
abstract class BaseFormFlowHiddenFieldExtension extends AbstractTypeExtension {

	/**
	 * {@inheritDoc}
	 */
	public function getExtendedType() {
		return HiddenType::class;
	}

	public static function _getExtendedTypes() {
		return [HiddenType::class];
	}

	/**
	 * {@inheritDoc}
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefined([
			'flow_instance_key',
			'flow_step_key',
		]);
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

// TODO revert to one clean class definition as soon as Symfony >= 5.0 is required
if (!method_exists(AbstractTypeExtension::class, 'getExtendedTypes')) {
	/**
	 * @author Konstantin Myakshin <koc-dp@yandex.ru>
	 * @author Christian Raue <christian.raue@gmail.com>
	 * @copyright 2011-2021 Christian Raue
	 * @license http://opensource.org/licenses/mit-license.php MIT License
	 */
	class FormFlowHiddenFieldExtension extends BaseFormFlowHiddenFieldExtension {
		public static function getExtendedTypes() {
			return self::_getExtendedTypes();
		}
	}
} else {
	/**
	 * @author Konstantin Myakshin <koc-dp@yandex.ru>
	 * @author Christian Raue <christian.raue@gmail.com>
	 * @copyright 2011-2021 Christian Raue
	 * @license http://opensource.org/licenses/mit-license.php MIT License
	 */
	class FormFlowHiddenFieldExtension extends BaseFormFlowHiddenFieldExtension {
		public static function getExtendedTypes() : iterable {
			return self::_getExtendedTypes();
		}
	}
}
