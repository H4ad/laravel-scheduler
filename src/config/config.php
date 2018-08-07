<?php

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

return [

	/**
	 * Define o nome da tabela que será gerada para os horários agendados.
	 */
	'schedules_table' => 'schedules',

	/**
	 * Configuração que habilita ou desabilita mensagens de erro ao tentar agendar
	 * um horário que já foi agendado.
	 *
	 * Ex: Duas pessoas tentam agendar no mesmo horário, a primeira consegue e a segunda
	 *  obtém uma mensagem de erro.
	 *
	 * Caso seja desabilitada, não será exibido mensagem de erro no caso acima e será
	 * registrado normalmente.
	 */
	'enable_schedule_conflict' => true,

	/**
	 * Configuração que habilita ou desabilita mensagens de erro ao tentar agendar
	 * um horário sem informar quando acaba (uma data final).
	 */
	'enable_schedule_without_end' => false,
];