<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Event\GetStepsEvent;
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Craue\FormFlowBundle\Form\Step;
use Craue\FormFlowBundle\Storage\SessionStorage;
use Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Topic;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @group unit
 *
 * @author Toni Uebernickel <tuebernickel@gmail.com>
 * @copyright 2011-2013 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowTest extends \PHPUnit_Framework_TestCase {

	public function testStepListener() {
		$steps = array(
			$this->getMock('\Craue\FormFlowBundle\Form\StepInterface'),
		);

		$dispatcher = new EventDispatcher();
		$dispatcher->addListener(FormFlowEvents::GET_STEPS, function(GetStepsEvent $event) use ($steps) {
			$event->setSteps($steps);

			$event->stopPropagation();
		});

		/* @var $flow \PHPUnit_Framework_MockObject_MockObject|\Craue\FormFlowBundle\Form\FormFlow */
		$flow = $this->getMockForAbstractClass('\Craue\FormFlowBundle\Form\FormFlow');
		$flow->setEventDispatcher($dispatcher);

		$this->assertEquals($steps, $flow->getSteps());
	}

	public function testStepWithBuiltForm() {
		$formFactory = Forms::createFormFactoryBuilder()
			->addExtension(new HttpFoundationExtension())
			->getFormFactory()
		;

		$options = array(
			'data_class' => 'Craue\FormFlowBundle\Tests\IntegrationTestBundle\Entity\Topic',
		);
		$form = $formFactory->createNamedBuilder('demo', 'form', null, $options)
			->add('title', 'text')
			->add('description', 'textarea')
			->getForm()
		;

		$step = new Step();
		$step->setNumber(1);
		$step->setType($form);
		$step->setLabel('Some Step Label');

		$storage = new SessionStorage(new Session(new MockArraySessionStorage()));

		$request = new Request(array(), array(
			'some_step_key' => 1,
			'demo' => array(
				'title' => 'Some topic title',
				'description' => 'This is a useless description.',
			),
		));
		$request->setMethod('POST');

		/* @var $flow \PHPUnit_Framework_MockObject_MockObject|\Craue\FormFlowBundle\Form\FormFlow */
		$flow = $this->getMockForAbstractClass('\Craue\FormFlowBundle\Form\FormFlow', array(), '', true, true, true, array(
			'getSteps',
		));
		$flow
			->expects($this->atLeastOnce())
			->method('getSteps')
			->will($this->returnValue(array($step)))
		;

		$flow->setRequest($request);
		$flow->setStorage($storage);
		$flow->setStepDataKey('some_flow_key');
		$flow->setFormStepKey('some_step_key');

		$data = new Topic();

		$flow->bind($data);
		$flowForm = $flow->createForm();
		$this->assertSame($form, $flowForm);

		$this->assertTrue($flow->isValid($flowForm));

		$flow->saveCurrentStepData($flowForm);
		$this->assertFalse($flow->nextStep());

		$this->assertEquals('Some topic title', $data->title);
		$this->assertEquals('This is a useless description.', $data->description);
	}
}
