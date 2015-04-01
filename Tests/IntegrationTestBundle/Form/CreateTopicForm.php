<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Topic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CreateTopicForm extends AbstractType {

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$isBugReport = $options['isBugReport'];

		switch ($options['flow_step']) {
			case 1:
				$builder->add('title');
				$builder->add('description', null, array(
					'required' => false,
				));
				$choices = Topic::getValidCategories();
				$builder->add('category', 'choice', array(
					'choices' => array_combine($choices, $choices),
					'empty_value' => '',
				));
				break;
			case 2:
				$builder->add('comment', 'textarea', array(
					'required' => false,
				));
				break;
			case 3:
				if ($isBugReport) {
					$builder->add('details', 'textarea');
				}
				break;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
			'isBugReport' => false,
		));
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
	public function getName() {
		return 'createTopic';
	}

}
