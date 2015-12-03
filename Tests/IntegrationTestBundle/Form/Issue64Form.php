<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue64Form extends AbstractType {

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$useFqcn = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');

		switch ($options['flow_step']) {
			case 1:
				$subForm = $builder->create('sub', $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\FormType' : 'form', array(
					'data_class' => 'Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Issue64SubData',
				));
				$subForm->add('prop1', $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\TextType' : 'text', array(
					'required' => true,
				));
				$builder->add($subForm);
				break;
			case 2:
			case 3:
				$subForm = $builder->create('sub', $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\FormType' : 'form', array(
					'data_class' => 'Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Issue64SubData',
				));
				$subForm->add('prop2', $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\TextType' : 'text', array(
					'required' => true,
				));
				$builder->add($subForm);
				break;
			case 4:
				// nothing
				break;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return $this->getBlockPrefix();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBlockPrefix() {
		return 'issue64';
	}

}
