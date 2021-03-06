<?php

return [
	'does_not_belong' => 'Não é possível remover um registro que não pertence a mesma [model] ou o mesmo [id] que o adicionou.',
	'cant_remove_by_date' => 'Não é possível remover um registro pelo horário marcado quando a configuração [enable_schedule_conflict] está desabilitada.',
	'cant_add_without_end' => 'Não é possível adicionar um registro sem o parâmetro [end_at] quando a configuração [enable_schedule_without_end] está desabilitada.',
	'cant_add_with_same_start_at' => 'Não é possível adicionar um registro com uma data de ínicio [start_at] que já existe enquanto a configuração [enable_schedule_conflict] estiver habilitada.',
	'end_cant_before_start' => 'A data que indica o final [end_at] não pode ser anterior a data de início [start_at].',
	'model_not_found' => 'Não foi encontrado resultados para [:model]',
	'int_invalid_argument' => 'Não é possivel transformar a data em inteiro para uma instância Carbon quando não há referência.'
];