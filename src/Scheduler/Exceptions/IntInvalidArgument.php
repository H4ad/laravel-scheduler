<?php namespace H4ad\Scheduler\Exceptions;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Symfony\Component\HttpFoundation\Response;

class IntInvalidArgument extends CustomException
{

	/**
	 * {@inheritDoc}
	 */
	protected $trans = 'int_invalid_argument';

	/**
	 * {@inheritDoc}
	 */
	protected $statusCode = Response::HTTP_BAD_REQUEST;
}