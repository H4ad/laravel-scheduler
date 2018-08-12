<?php namespace H4ad\Scheduler\Tests\Unit;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Illuminate\Support\Carbon;
use H4ad\Scheduler\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use H4ad\Scheduler\Tests\Unit\SampleModel;
use H4ad\Scheduler\Tests\Unit\ScenarioOneTest;
use H4ad\Scheduler\Exceptions\CantRemoveByDate;

/**
 * Configurações para os testes dessa classe:
 *  - [enable_schedule_conflict] = false
 *  - [enable_schedule_without_end] = false
 */
class ScenarioTwoTest extends ScenarioOneTest
{
    /**
     * Sempre haverá testes padrões que devem estar corretos em todos os cenários.
     * Essa é a váriavel que será usada que irá garantir isso e evitar duplicação de código.
     *
     * @var ScenarioOneTest
     */
    protected $scenarioOne;

    /**
     * {@inheritDoc}
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        Config::set('scheduler.enable_schedule_conflict', false);
        Config::set('scheduler.enable_schedule_without_end', false);
    }

    /**
     * Estou sobrescrevendo esse método para testar o mesmo método
     * só que dessa vez ele não será lançado uma exceção pois a configuração
     * [enable_schedule_conflict] está desabilitada.
     *
     * @return void
     */
    public function testAddScheduleThrowsExceptionForDuplicated()
    {
        $stringInt = $this->sampleModel->addSchedule('2018-07-11 08:00:00', 45);
        $this->assertDatabaseHas(Config::get('scheduler.schedules_table'), $stringInt->toArray());

        $duplicated = $this->sampleModel->addSchedule('2018-07-11 08:00:00', 45);
        $this->assertDatabaseHas(Config::get('scheduler.schedules_table'), $duplicated->toArray());
    }

    /**
     * Testa a remoção de um horário pela sua data de início em string
     * com as configurações de [enable_schedule_conflict] desabilitada.
     *
     * @return void
     */
    public function testRemoveScheduleByDateString()
    {
        $schedule = $this->sampleModel->addSchedule(Carbon::now(), Carbon::now()->addMinutes(30));

        $this->expectException(CantRemoveByDate::class);
        $this->sampleModel->removeSchedule($schedule->start_at->toDateTimeString());
    }

    /**
     * Testa a remoção de um horário pela sua data de início em Carbon
     * com as configurações de [enable_schedule_conflict] desabilitada.
     *
     * @return void
     */
    public function testRemoveScheduleByDateCarbon()
    {
        $schedule = $this->sampleModel->addSchedule(Carbon::now(), Carbon::now()->addMinutes(30));

        $this->expectException(CantRemoveByDate::class);
        $this->sampleModel->removeSchedule($schedule->start_at);
    }
}