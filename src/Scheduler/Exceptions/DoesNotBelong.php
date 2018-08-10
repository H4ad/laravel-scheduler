<?php namespace H4ad\Scheduler\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

class DoesNotBelong extends CustomException
{

	/**
	 * {@inheritDoc}
	 */
	protected $trans = 'does_not_belong';

	/**
	 * {@inheritDoc}
	 */
	protected $statusCode = Response::HTTP_FORBIDDEN;
}