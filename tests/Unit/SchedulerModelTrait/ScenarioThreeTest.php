<?php namespace H4ad\Scheduler\Tests\Unit;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

/**
 * Configurações para os testes dessa classe:
 *  - [enable_schedule_conflict] = true
 *  - [enable_schedule_without_end] = true
 */
class ScenarioThreeTest extends ScenarioOneTest
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

        Config::set('scheduler.enable_schedule_conflict', true);
        Config::set('scheduler.enable_schedule_without_end', true);
    }

    /**
     * Estou sobrescrevendo esse método para testar o mesmo método
     * só que dessa vez ele não será lançado uma exceção pois a configuração
     * [enable_schedule_without_end] está habilitada.
     *
     * @return void
     */
    public function testAddScheduleThrowsExceptionForNoEnd()
    {
        $withoutEnd = $this->sampleModel->addSchedule(Carbon::now());
        $this->assertDatabaseHas(Config::get('scheduler.schedules_table'), $withoutEnd->toArray());
    }
}