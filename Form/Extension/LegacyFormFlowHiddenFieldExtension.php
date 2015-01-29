<?php

namespace Craue\FormFlowBundle\Form\Extension;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class LegacyFormFlowHiddenFieldExtension extends FormFlowHiddenFieldExtension {

	/**
	 * {@inheritDoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		parent::configureOptions($resolver);
	}

}
