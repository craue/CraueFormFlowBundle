<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Topic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
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
				$builder->add('description', null, [
					'required' => false,
				]);
				$choices = Topic::getValidCategories();
				$builder->add('category', ChoiceType::class, [
					'choices' => array_combine($choices, $choices),
					'placeholder' => '',
				]);
				break;
			case 2:
				$builder->add('comment', TextareaType::class, [
					'required' => false,
				]);
				break;
			case 3:
				if ($isBugReport) {
					$builder->add('details', TextareaType::class);
				}
				break;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'isBugReport' => false,
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBlockPrefix() {
		return 'createTopic';
	}

}
