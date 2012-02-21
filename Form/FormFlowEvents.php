<?php

namespace Craue\FormFlowBundle\Form;

/**
 * @author Marcus Stöhr <dafish@soundtrack-board.de>
 * @copyright 2011-2012 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowEvents {

	const PRE_BIND = 'flow.pre_bind';

	const POST_BIND_REQUEST = 'flow.post_bind_request';

	const POST_BIND_SAVED_DATA = 'flow.post_bind_saved_data';

	const POST_VALIDATE = 'flow.post_validate';

}
