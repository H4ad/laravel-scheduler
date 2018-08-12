<?php namespace H4ad\Scheduler\Exceptions;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Symfony\Component\HttpFoundation\Response;

class ModelNotFound extends CustomException
{

	/**
	 * {@inheritDoc}
	 */
	protected $trans = 'model_not_found';

	/**
	 * {@inheritDoc}
	 */
	protected $statusCode = Response::HTTP_NOT_FOUND;
}