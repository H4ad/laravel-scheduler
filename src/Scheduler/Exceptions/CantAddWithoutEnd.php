<?php namespace H4ad\Scheduler\Exceptions;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Symfony\Component\HttpFoundation\Response;

class CantAddWithoutEnd extends CustomException
{

	/**
	 * {@inheritDoc}
	 */
	protected $trans = 'cant_add_without_end';

	/**
	 * {@inheritDoc}
	 */
	protected $statusCode = Response::HTTP_BAD_REQUEST;
}