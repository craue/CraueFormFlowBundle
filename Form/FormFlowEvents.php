<?php

namespace Craue\FormFlowBundle\Form;

/**
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @copyright 2011-2017 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowEvents {

	const PRE_BIND = 'flow.pre_bind';

	const GET_STEPS = 'flow.get_steps';

	const POST_BIND_SAVED_DATA = 'flow.post_bind_saved_data';

	const POST_BIND_FLOW = 'flow.post_bind_flow';

	const FLOW_EXPIRED  = 'flow.flow_expired';

	const POST_BIND_REQUEST = 'flow.post_bind_request';

	const PREVIOUS_STEP_INVALID = 'flow.previous_step_invalid';

	const POST_VALIDATE = 'flow.post_validate';

}
