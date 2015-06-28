<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Issue149SubData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue149Form extends AbstractType {

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		switch ($options['flow_step']) {
			case 1:
				$subForm = $builder->create('photo', 'form', array(
					'data_class' => get_class(new Issue149SubData()),
				));
				$subForm->add('image', 'file');
				$subForm->add('title');
				$builder->add($subForm);
				break;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'issue149';
	}

}
