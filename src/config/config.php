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
	 * Horário de abertura que será usado de referencia para fazer as consultas de horários disponiveis.
	 */
	'opening_time' => '06:00:00',

	/**
	 * Horário de fechamento que será usado de referencia para fazer as consultas de horários disponiveis.
	 */
	'closing_time' => '20:00:00',

	/**
	 * Define o nome da tabela que será gerada para os horários agendados.
	 */
	'schedules_table' => 'schedules',

	/**
	 * Define o nome da tabela que será gerada para os status do horários agendados.
	 */
	'schedule_status_table' => 'schedule_status',

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