<?php

namespace Craue\FormFlowBundle\Tests\Resources;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Storage\DataManager;
use Craue\FormFlowBundle\Storage\SessionStorage;
use Craue\FormFlowBundle\Tests\IntegrationTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class TemplateRenderingTest extends IntegrationTestCase {

	const BUTTONS_TEMPLATE = '@CraueFormFlow/FormFlow/buttons.html.twig';
	const STEP_LIST_TEMPLATE = '@CraueFormFlow/FormFlow/stepList.html.twig';

	public function testButtons() {
		$flow = $this->getFlowStub();

		// first step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, [
			'flow' => $flow,
		]);

		$this->assertContains('<div class="craue_formflow_buttons craue_formflow_button_count_2">', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_last">next</button>', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_first" name="flow_renderingTest_transition" value="reset" formnovalidate="formnovalidate">start over</button>', $renderedTemplate);

		// next step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, [
			'flow' => $flow,
		]);

		$this->assertContains('<div class="craue_formflow_buttons craue_formflow_button_count_3">', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_last">finish</button>', $renderedTemplate);
		$this->assertContains('<button type="submit" class="" name="flow_renderingTest_transition" value="back" formnovalidate="formnovalidate">back</button>', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_first" name="flow_renderingTest_transition" value="reset" formnovalidate="formnovalidate">start over</button>', $renderedTemplate);
	}

	public function testButtons_noResetButton() {
		$flow = $this->getFlowStub();

		// first step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, [
			'craue_formflow_button_render_reset' => false,
			'flow' => $flow,
		]);
		$this->assertContains('<div class="craue_formflow_buttons craue_formflow_button_count_1">', $renderedTemplate);
		$this->assertNotContains('<button type="submit" class="craue_formflow_button_first" name="flow_renderingTest_transition" value="reset" formnovalidate="formnovalidate">start over</button>', $renderedTemplate);

		// second step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, [
			'craue_formflow_button_render_reset' => false,
			'flow' => $flow,
		]);
		$this->assertContains('<div class="craue_formflow_buttons craue_formflow_button_count_2">', $renderedTemplate);
		$this->assertNotContains('<button type="submit" class="craue_formflow_button_first" name="flow_renderingTest_transition" value="reset" formnovalidate="formnovalidate">start over</button>', $renderedTemplate);
	}

	public function testButtons_firstStepSkipped() {
		$flow = $this->getFlowStub([], [
			[
				'label' => 'step1',
				'skip' => true,
			],
			[
				'label' => 'step2',
			],
		]);

		// second step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, [
			'flow' => $flow,
		]);

		$this->assertContains('<div class="craue_formflow_buttons craue_formflow_button_count_2">', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_last">finish</button>', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_first" name="flow_renderingTest_transition" value="reset" formnovalidate="formnovalidate">start over</button>', $renderedTemplate);
	}

	public function testButtons_onlyOneStep() {
		$flow = $this->getFlowStub([], [
			[
				'label' => 'step1',
			],
		]);

		// first step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, [
			'flow' => $flow,
		]);

		$this->assertContains('<div class="craue_formflow_buttons craue_formflow_button_count_2">', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_last">finish</button>', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_first" name="flow_renderingTest_transition" value="reset" formnovalidate="formnovalidate">start over</button>', $renderedTemplate);
	}

	/**
	 * @dataProvider dataCustomizedButton
	 */
	public function testCustomizedButton($numberOfSteps, $jumpToStep, array $parameters, $expectedHtml) {
		$flow = $this->getFlowStub([], array_fill_keys(range(1, $numberOfSteps), []));

		do {
			$flow->nextStep();
		} while (--$jumpToStep > 0);

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, array_merge($parameters, [
			'flow' => $flow,
		]));

		$this->assertContains($expectedHtml, $renderedTemplate);
	}

	public function dataCustomizedButton() {
		return [
			'next button custom class' => [
				2, 1,
				['craue_formflow_button_class_next' => 'next'],
				'<button type="submit" class="next">next</button>',
			],
			'next button custom label' => [
				2, 1,
				['craue_formflow_button_label_next' => 'custom next'],
				'<button type="submit" class="craue_formflow_button_last">custom next</button>',
			],
			'finish button custom class' => [
				1, 1,
				['craue_formflow_button_class_finish' => 'finish'],
				'<button type="submit" class="finish">finish</button>',
			],
			'finish button custom label' => [
				1, 1,
				['craue_formflow_button_label_finish' => 'custom finish'],
				'<button type="submit" class="craue_formflow_button_last">custom finish</button>',
			],
			'last button custom class (finish)' => [
				1, 1,
				['craue_formflow_button_class_last' => 'last'],
				'<button type="submit" class="last">finish</button>',
			],
			'last button custom label (finish)' => [
				1, 1,
				['craue_formflow_button_label_last' => 'custom last'],
				'<button type="submit" class="craue_formflow_button_last">custom last</button>',
			],
			'last button custom class (next)' => [
				2, 1,
				['craue_formflow_button_class_last' => 'last'],
				'<button type="submit" class="last">next</button>',
			],
			'last button custom label (next)' => [
				2, 1,
				['craue_formflow_button_label_last' => 'custom last'],
				'<button type="submit" class="craue_formflow_button_last">custom last</button>',
			],
			'back button custom class' => [
				2, 2,
				['craue_formflow_button_class_back' => 'back'],
				'<button type="submit" class="back" name="flow_renderingTest_transition" value="back" formnovalidate="formnovalidate">back</button>',
			],
			'back button custom label' => [
				2, 2,
				['craue_formflow_button_label_back' => 'custom back'],
				'<button type="submit" class="" name="flow_renderingTest_transition" value="back" formnovalidate="formnovalidate">custom back</button>',
			],
			'reset button custom class' => [
				1, 1,
				['craue_formflow_button_class_reset' => 'reset'],
				'<button type="submit" class="reset" name="flow_renderingTest_transition" value="reset" formnovalidate="formnovalidate">start over</button>',
			],
			'reset button custom label' => [
				1, 1,
				['craue_formflow_button_label_reset' => 'custom reset'],
				'<button type="submit" class="craue_formflow_button_first" name="flow_renderingTest_transition" value="reset" formnovalidate="formnovalidate">custom reset</button>',
			],
		];
	}

	public function testStepList() {
		$flow = $this->getFlowStub();

		// first step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::STEP_LIST_TEMPLATE, [
			'flow' => $flow,
		]);

		$this->assertContains('<ol class="craue_formflow_steplist">', $renderedTemplate);
		$this->assertContains('<li class="craue_formflow_current_step">step1</li>', $renderedTemplate);
		$this->assertContains('<li>step2</li>', $renderedTemplate);

		// next step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::STEP_LIST_TEMPLATE, [
			'flow' => $flow,
		]);

		$this->assertContains('<li>step1</li>', $renderedTemplate);
		$this->assertContains('<li class="craue_formflow_current_step">step2</li>', $renderedTemplate);
	}

	public function testStepList_stepDone() {
		$flow = $this->getFlowStub(['isStepDone']);

		// second step
		$flow->nextStep();
		$flow->nextStep();

		$flow
			->expects($this->once())
			->method('isStepDone')
			->will($this->returnValue(true))
		;

		$renderedTemplate = $this->getTwig()->render(self::STEP_LIST_TEMPLATE, [
			'flow' => $flow,
		]);

		$this->assertContains('<li class="craue_formflow_done_step">step1</li>', $renderedTemplate);
	}

	public function testStepList_stepSkipped() {
		$flow = $this->getFlowStub([], [
			[
				'label' => 'step1',
				'skip' => true,
			],
			[
				'label' => 'step2',
			],
		]);

		// second step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::STEP_LIST_TEMPLATE, [
			'flow' => $flow,
		]);

		$this->assertContains('<li class="craue_formflow_skipped_step">step1</li>', $renderedTemplate);
		$this->assertContains('<li class="craue_formflow_current_step">step2</li>', $renderedTemplate);
	}

	/**
	 * @param string[] $stubbedMethods names of additionally stubbed methods
	 * @param array $stepsConfig steps config
	 * @return MockObject|FormFlow
	 */
	protected function getFlowStub(array $stubbedMethods = [], array $stepsConfig = null) {
		/* @var $flow MockObject|FormFlow */
		$flow = $this->getMockBuilder(FormFlow::class)->setMethods(array_merge(['getName', 'loadStepsConfig'], $stubbedMethods))->getMock();

		$flow->setDataManager(new DataManager(new SessionStorage(new Session(new MockArraySessionStorage()))));

		$flow
			->method('getName')
			->will($this->returnValue('renderingTest'))
		;

		if ($stepsConfig === null) {
			$stepsConfig = [
				1 => [
					'label' => 'step1',
				],
				2 => [
					'label' => 'step2',
				],
			];
		}

		$flow
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue($stepsConfig))
		;

		return $flow;
	}

}
