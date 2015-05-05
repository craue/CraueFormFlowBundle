<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class AdditionalValidationGroupsPerStepForm extends AbstractType {

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('value');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'additionalValidationGroupsPerStep';
	}

}
