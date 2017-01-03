<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PhotoUploadFlow extends FormFlow {

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		$useFqcn = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
		$formType = $useFqcn ? 'Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form\PhotoUploadForm' : 'photoUpload';

		return array(
			array(
				'label' => 'select a photo',
				'form_type' => $formType,
			),
			array(
				'label' => 'describe the photo',
				'form_type' => $formType,
			),
			array(
				'label' => 'confirmation',
				'form_type' => $formType,
			),
		);
	}

}
