<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\PhotoUpload;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2022 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PhotoCollectionUploadForm extends AbstractType {

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) : void {
		switch ($options['flow_step']) {
			case 1:
				$builder->add('photos', CollectionType::class, [
					'entry_type' => PhotoCompleteUploadForm::class,
					'allow_add' => true,
				]);
				break;
			case 2:
				$builder->add('comment', null, [
					'required' => false,
				]);
				break;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBlockPrefix() : string {
		return 'photoCollectionUpload';
	}

}
