<?php
/**
 * Bad Request Exception
 *
 *
 * @package       API
 * @author        Marc Towler <marc@marctowler.co.uk>
 * @copyright     Copyright (c) 2018 Marc Towler
 * @license       https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link          https://api.itslit.uk
 * @since         Version 1.1
 * @filesource
 */

namespace API\Exceptions;


class BadRequest extends \Exception
{
	public function __construct()
	{
		parent::__construct('An invalid request was passed through to the API.');
	}
}