<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Issue149Form extends AbstractType {

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$useFqcn = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');

		switch ($options['flow_step']) {
			case 1:
				$subForm = $builder->create('photo', $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\FormType' : 'form', array(
					'data_class' => 'Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Issue149SubData',
				));
				$subForm->add('image', $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\FileType' : 'file');
				$subForm->add('title');
				$builder->add($subForm);
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
		return 'issue149';
	}

}
