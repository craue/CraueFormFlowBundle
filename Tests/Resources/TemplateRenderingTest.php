<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Storage\DataManager;
use Craue\FormFlowBundle\Storage\SessionStorage;
use Craue\FormFlowBundle\Tests\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @group integration
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2015 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class TemplateRenderingTest extends IntegrationTestCase {

	const BUTTONS_TEMPLATE = 'CraueFormFlowBundle:FormFlow:buttons.html.twig';
	const STEP_LIST_TEMPLATE = 'CraueFormFlowBundle:FormFlow:stepList.html.twig';

	public function testButtons() {
		$flow = $this->getFlowStub();

		// first step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, array(
			'flow' => $flow,
		));

		$this->assertContains('<div class="craue_formflow_buttons craue_formflow_button_count_2">', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_last">next</button>', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_first" name="flow_renderingTest_transition" value="reset" formnovalidate="formnovalidate">start over</button>', $renderedTemplate);

		// next step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, array(
			'flow' => $flow,
		));

		$this->assertContains('<div class="craue_formflow_buttons craue_formflow_button_count_3">', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_last">finish</button>', $renderedTemplate);
		$this->assertContains('<button type="submit" class="" name="flow_renderingTest_transition" value="back" formnovalidate="formnovalidate">back</button>', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_first" name="flow_renderingTest_transition" value="reset" formnovalidate="formnovalidate">start over</button>', $renderedTemplate);
	}

	public function testButtons_firstStepSkipped() {
		$flow = $this->getFlowStub(array(), array(
			array(
				'label' => 'step1',
				'skip' => true,
			),
			array(
				'label' => 'step2',
			),
		));

		// second step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, array(
			'flow' => $flow,
		));

		$this->assertContains('<div class="craue_formflow_buttons craue_formflow_button_count_2">', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_last">finish</button>', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_first" name="flow_renderingTest_transition" value="reset" formnovalidate="formnovalidate">start over</button>', $renderedTemplate);
	}

	public function testButtons_onlyOneStep() {
		$flow = $this->getFlowStub(array(), array(
			array(
				'label' => 'step1',
			),
		));

		// first step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, array(
			'flow' => $flow,
		));

		$this->assertContains('<div class="craue_formflow_buttons craue_formflow_button_count_2">', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_last">finish</button>', $renderedTemplate);
		$this->assertContains('<button type="submit" class="craue_formflow_button_first" name="flow_renderingTest_transition" value="reset" formnovalidate="formnovalidate">start over</button>', $renderedTemplate);
	}

	public function testCustomNextButtonClass() {
		$flow = $this->getFlowStub(array(), array(
			array(
				'label' => 'step1',
			),
			array(
				'label' => 'step2',
			),
		));

		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, array(
			'flow' => $flow,
			'craue_formflow_button_class_next' => 'next',
		));

		$this->assertContains('<button type="submit" class="next">next</button>', $renderedTemplate);
	}

	public function testCustomFinishButtonClass() {
		$flow = $this->getFlowStub(array(), array(
			array(
				'label' => 'step1',
			),
		));

		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, array(
			'flow' => $flow,
			'craue_formflow_button_class_finish' => 'finish',
		));

		$this->assertContains('<button type="submit" class="finish">finish</button>', $renderedTemplate);
	}

	public function testCustomLastButtonClass() {
		$flow = $this->getFlowStub(array(), array(
			array(
				'label' => 'step1',
			),
		));

		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, array(
			'flow' => $flow,
			'craue_formflow_button_class_last' => 'last',
		));

		$this->assertContains('<button type="submit" class="last">finish</button>', $renderedTemplate);

		$flow = $this->getFlowStub(array(), array(
			array(
				'label' => 'step1',
			),
			array(
				'label' => 'step2',
			),
		));

		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, array(
			'flow' => $flow,
			'craue_formflow_button_class_last' => 'last',
		));

		$this->assertContains('<button type="submit" class="last">next</button>', $renderedTemplate);
	}

	public function testCustomBackButtonClass() {
		$flow = $this->getFlowStub(array(), array(
			array(
				'label' => 'step1',
			),
			array(
				'label' => 'step2',
			),
		));

		$flow->nextStep();
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, array(
			'flow' => $flow,
			'craue_formflow_button_class_back' => 'back',
		));

		$this->assertContains('<button type="submit" class="back" name="flow_renderingTest_transition" value="back" formnovalidate="formnovalidate">back</button>', $renderedTemplate);
	}

	public function testCustomResetButtonClass() {
		$flow = $this->getFlowStub(array(), array(
			array(
				'label' => 'step1',
			),
		));

		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::BUTTONS_TEMPLATE, array(
			'flow' => $flow,
			'craue_formflow_button_class_reset' => 'reset',
		));

		$this->assertContains('<button type="submit" class="reset" name="flow_renderingTest_transition" value="reset" formnovalidate="formnovalidate">start over</button>', $renderedTemplate);
	}

	public function testStepList() {
		$flow = $this->getFlowStub();

		// first step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::STEP_LIST_TEMPLATE, array(
			'flow' => $flow,
		));

		$this->assertContains('<ol class="craue_formflow_steplist">', $renderedTemplate);
		$this->assertContains('<li class="craue_formflow_current_step">step1</li>', $renderedTemplate);
		$this->assertContains('<li>step2</li>', $renderedTemplate);

		// next step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::STEP_LIST_TEMPLATE, array(
			'flow' => $flow,
		));

		$this->assertContains('<li>step1</li>', $renderedTemplate);
		$this->assertContains('<li class="craue_formflow_current_step">step2</li>', $renderedTemplate);
	}

	public function testStepList_stepDone() {
		$flow = $this->getFlowStub(array('isStepDone'));

		// second step
		$flow->nextStep();
		$flow->nextStep();

		$flow
			->expects($this->once())
			->method('isStepDone')
			->will($this->returnValue(true))
		;

		$renderedTemplate = $this->getTwig()->render(self::STEP_LIST_TEMPLATE, array(
			'flow' => $flow,
		));

		$this->assertContains('<li class="craue_formflow_done_step">step1</li>', $renderedTemplate);
	}

	public function testStepList_stepSkipped() {
		$flow = $this->getFlowStub(array(), array(
			array(
				'label' => 'step1',
				'skip' => true,
			),
			array(
				'label' => 'step2',
			),
		));

		// second step
		$flow->nextStep();

		$renderedTemplate = $this->getTwig()->render(self::STEP_LIST_TEMPLATE, array(
			'flow' => $flow,
		));

		$this->assertContains('<li class="craue_formflow_skipped_step">step1</li>', $renderedTemplate);
		$this->assertContains('<li class="craue_formflow_current_step">step2</li>', $renderedTemplate);
	}

	/**
	 * @param string[] $stubbedMethods names of additionally stubbed methods
	 * @param array $stepsConfig steps config
	 * @return \PHPUnit_Framework_MockObject_MockObject|FormFlow
	 */
	protected function getFlowStub(array $stubbedMethods = array(), array $stepsConfig = null) {
		/* @var $flow \PHPUnit_Framework_MockObject_MockObject|FormFlow */
		$flow = $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', array_merge(array('getName', 'loadStepsConfig'), $stubbedMethods));

		$flow->setDataManager(new DataManager(new SessionStorage(new Session(new MockArraySessionStorage()))));

		$flow
			->expects($this->any())
			->method('getName')
			->will($this->returnValue('renderingTest'))
		;

		if ($stepsConfig === null) {
			$stepsConfig = array(
				1 => array(
					'label' => 'step1',
				),
				2 => array(
					'label' => 'step2',
				),
			);
		}

		$flow
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue($stepsConfig))
		;

		return $flow;
	}

}
