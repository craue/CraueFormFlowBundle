<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Topic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CreateTopicForm extends AbstractType {

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$useFqcn = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix'); // Symfony's Form component >=2.8
		$isBugReport = $options['isBugReport'];
		$setChoicesAsValuesOption = $useFqcn && method_exists('Symfony\Component\Form\AbstractType', 'getName'); // Symfony's Form component >=2.8 && <3.0

		switch ($options['flow_step']) {
			case 1:
				$builder->add('title');
				$builder->add('description', null, array(
					'required' => false,
				));
				$defaultChoiceOptions = array();
				if ($setChoicesAsValuesOption) {
					$defaultChoiceOptions['choices_as_values'] = true;
				}
				$choices = Topic::getValidCategories();
				$builder->add('category', $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\ChoiceType' : 'choice', array_merge($defaultChoiceOptions, array(
					'choices' => array_combine($choices, $choices),
					'placeholder' => '',
				)));
				break;
			case 2:
				$builder->add('comment', $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\TextareaType' : 'textarea', array(
					'required' => false,
				));
				break;
			case 3:
				if ($isBugReport) {
					$builder->add('details', $useFqcn ? 'Symfony\Component\Form\Extension\Core\Type\TextareaType' : 'textarea');
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
	public function getName() {
		return $this->getBlockPrefix();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBlockPrefix() {
		return 'createTopic';
	}

}
