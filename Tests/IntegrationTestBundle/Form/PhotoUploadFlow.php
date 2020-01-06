<?php

namespace Craue\FormFlowBundle\Tests\IntegrationTestBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PhotoUploadFlow extends FormFlow {

	/**
	 * {@inheritDoc}
	 */
	protected function loadStepsConfig() {
		$formType = PhotoUploadForm::class;

		return [
			[
				'label' => 'select a photo',
				'form_type' => $formType,
			],
			[
				'label' => 'describe the photo',
				'form_type' => $formType,
			],
			[
				'label' => 'confirmation',
				'form_type' => $formType,
			],
		];
	}

}
