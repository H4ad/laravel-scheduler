<?php namespace H4ad\Scheduler\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

class CantRemoveByDate extends CustomException
{

	/**
	 * {@inheritDoc}
	 */
	protected $trans = 'cant_remove_by_date';

	/**
	 * {@inheritDoc}
	 */
	protected $statusCode = Response::HTTP_BAD_REQUEST;
}