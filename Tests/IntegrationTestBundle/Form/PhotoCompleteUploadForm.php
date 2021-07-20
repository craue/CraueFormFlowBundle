<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2021 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PhotoCompleteUploadForm extends AbstractType {

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('photo', FileType::class);
        $builder->add('comment', null, [
            'required' => false,
        ]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBlockPrefix() {
		return 'photoCompleteUpload';
	}

}
