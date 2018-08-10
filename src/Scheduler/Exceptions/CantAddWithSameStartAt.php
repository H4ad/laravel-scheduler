<?php namespace H4ad\Scheduler\Exceptions;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Symfony\Component\HttpFoundation\Response;

class CantAddWithSameStartAt extends CustomException
{

	/**
	 * {@inheritDoc}
	 */
	protected $trans = 'cant_add_with_same_start_at';

	/**
	 * {@inheritDoc}
	 */
	protected $statusCode = Response::HTTP_BAD_REQUEST;
}