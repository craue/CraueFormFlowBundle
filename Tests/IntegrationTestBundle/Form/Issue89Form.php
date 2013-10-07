<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2013 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue89Form extends AbstractType {

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		switch ($options['flow_step']) {
			case 1:
				$builder->add('prop1', 'text', array(
					'required' => true,
				));
				$builder->add('prop2', 'text', array(
					'required' => true,
					'data' => 'default value',
				));
				break;
			case 2:
				// nothing
				break;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'issue89';
	}

}
