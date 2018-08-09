# TODOS

- Em Scheduler\Contracts\SchedulerModelTrait definir o tipo de retorno para as funções:
	* availableToday
	* availableOn

Anotação mental: Crie talvez uma classe que irá tomar conta de retornar os resultados da
	função. Usar uma classe forneceria a opção de criar métodos auxiliares.
	Ou talvez use apenas um array, só que o problema é decidir como exibir os horários
	(ex: Das 10 as 15 hrs está tudo livre, se for passado uma duração, tudo bem, mas se não, como será exibido ?)
	Esse mesmo exemplo serve no caso da classe, como lidar com isso ?