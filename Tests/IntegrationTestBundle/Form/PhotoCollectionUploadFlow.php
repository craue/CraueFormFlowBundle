<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2022 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PhotoCollectionUploadFlow extends FormFlow {

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		$formType = PhotoCollectionUploadForm::class;

		return [
			[
				'label' => 'select your photos',
				'form_type' => $formType,
			],
			[
				'label' => 'describe the photos',
				'form_type' => $formType,
			],
			[
				'label' => 'confirmation',
				'form_type' => $formType,
			],
		];
	}

}
