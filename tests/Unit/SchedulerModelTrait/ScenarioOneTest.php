<?php namespace H4ad\Scheduler\Tests\Unit;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Carbon\Carbon;
use H4ad\Scheduler\Tests\TestCase;
use H4ad\Scheduler\Facades\Scheduler;
use Illuminate\Support\Facades\Config;
use H4ad\Scheduler\Tests\Unit\SampleModel;
use H4ad\Scheduler\Exceptions\ModelNotFound;
use H4ad\Scheduler\Exceptions\DoesNotBelong;
use H4ad\Scheduler\Exceptions\CantAddWithoutEnd;
use H4ad\Scheduler\Exceptions\EndCantBeforeStart;
use H4ad\Scheduler\Exceptions\CantAddWithSameStartAt;

/**
 * Configurações para os testes dessa classe:
 * 	- [enable_schedule_conflict] = true
 *  - [enable_schedule_without_end] = false
 */
class ScenarioOneTest extends TestCase
{
	/**
	 * {@inheritDoc}
	 */
	protected function getEnvironmentSetUp($app)
    {
    	parent::getEnvironmentSetUp($app);

        Config::set('scheduler.enable_schedule_conflict', true);
        Config::set('scheduler.enable_schedule_without_end', false);
    }

    /**
     * Testa a adição de um horário com [start_at] em string e [end_at] em int.
     *
     * @return void
     */
    public function testAddScheduleStringInt()
    {
    	$stringInt = $this->sampleModel->addSchedule('2018-07-11 09:00:00', 45);
    	$this->assertDatabaseHas(Config::get('scheduler.schedules_table'), $stringInt->toArray());
    }

    /**
     * Testa a adição de um horário com o [start_at] em string e [end_at] em string.
     *
     * @return void
     */
    public function testAddScheduleStringString()
    {
    	$stringString = $this->sampleModel->addSchedule('2018-07-11 10:00:00', '2018-07-11 10:30:00');
    	$this->assertDatabaseHas(Config::get('scheduler.schedules_table'), $stringString->toArray());
    }

    /**
     * Testa a adição de um horário com o [start_at] em Carbon e [end_at] em Carbon.
     *
     * @return void
     */
    public function testAddScheduleCarbonCarbon()
    {
    	$carbonCarbon =  $this->sampleModel->addSchedule(Carbon::now(), Carbon::now()->addMinutes(30));
    	$this->assertDatabaseHas(Config::get('scheduler.schedules_table'), $carbonCarbon->toArray());
    }

    /**
     * Testa a adição de um horário com um status.
     *
     * @return void
     */
    public function testAddScheduleWithStatus()
    {
    	$withStatus = $this->sampleModel->addSchedule(Carbon::now(), 45, $this->getScheduleStatus()->id);
    	$this->assertDatabaseHas(Config::get('scheduler.schedules_table'), $withStatus->toArray());
    }

    /**
     * Testa a adição de um horário com o [start_at] e [end_at] sendo iguais.
     *
     * @return void
     */
    public function testAddScheduleWithSameStartAndEndDate()
    {
    	$date = Carbon::now();
    	$sameStartAndDate = $this->sampleModel->addSchedule($date, $date);
    	$this->assertDatabaseHas(Config::get('scheduler.schedules_table'), $sameStartAndDate->toArray());
    }

	/**
     * Testa a adicão de um horário sem o [end_at].
     *
     * @return void
     */
    public function testAddScheduleThrowsExceptionForNoEnd()
    {
    	$this->expectException(CantAddWithoutEnd::class);
    	$this->sampleModel->addSchedule(Carbon::now());
    }

    /**
     * Testa a adição de dois horários com a mesma data de início.
     *
     * @return void
     */
    public function testAddScheduleThrowsExceptionForDuplicated()
    {
    	$stringInt = $this->sampleModel->addSchedule('2018-07-11 08:00:00', 45);
    	$this->assertDatabaseHas(Config::get('scheduler.schedules_table'), $stringInt->toArray());

    	$this->expectException(CantAddWithSameStartAt::class);
    	$this->sampleModel->addSchedule('2018-07-11 08:00:00', 45);
    }

    /**
     * Testa a adição de um horário com o [end_at] sendo anterior ao [start_at].
     *
     * @return void
     */
    public function testAddScheduleThrowsExceptionEndComingBeforeStart()
    {
    	$this->expectException(EndCantBeforeStart::class);
    	$this->sampleModel->addSchedule(Carbon::now(), Carbon::now()->subMinutes(30));
    }

    /**
     * Testes para o método de remover um horário.
     */

    /**
     * Testa a remoção de um horário pelo seu id.
     *
     * @return void
     */
    public function testRemoveScheduleById()
    {
        $schedule = $this->sampleModel->addSchedule(Carbon::now(), Carbon::now()->addMinutes(30));
        $this->assertEquals(true, $this->sampleModel->removeSchedule($schedule->id));
    }

    /**
     * Testa a remoção de um horário pela sua data de início em string.
     *
     * @return void
     */
    public function testRemoveScheduleByDateString()
    {
        $schedule = $this->sampleModel->addSchedule(Carbon::now(), Carbon::now()->addMinutes(30));
        $this->assertEquals(true, $this->sampleModel->removeSchedule($schedule->start_at->toDateTimeString()));
    }

    /**
     * Testa a remoção de um horário pela sua data de início em Carbon.
     *
     * @return void
     */
    public function testRemoveScheduleByDateCarbon()
    {
        $schedule = $this->sampleModel->addSchedule(Carbon::now(), Carbon::now()->addMinutes(30));
        $this->assertEquals(true, $this->sampleModel->removeSchedule($schedule->start_at));
    }

    /**
     * Testa a remoção de um horário registrado por uma model por outra model com implementação
     * do SchedulerModelTrait.
     *
     * @return void
     */
    public function testRemoveScheduleOfOtherModel()
    {
        $scheduleFake = $this->sampleModelFake->addSchedule(Carbon::now(), Carbon::now()->addMinutes(30));

        $this->expectException(DoesNotBelong::class);
        $this->sampleModel->removeSchedule($scheduleFake->id);
    }

    /**
     * Testa a remoção de um horário registrado pelo mesmo [model_type] mas com um [id] diferente.
     *
     * @return void
     */
    public function testRemoveScheduleOfSameModelTypeAndDifferentId()
    {
        $schedule = $this->sampleModel->addSchedule(Carbon::now(), Carbon::now()->addMinutes(30));
        $sampleModelTwo = SampleModel::create();

        $this->expectException(DoesNotBelong::class);
        $sampleModelTwo->removeSchedule($schedule->id);
    }

    /**
     * Testa a remoção de um horário que não existe.
     *
     * @return void
     */
    public function testRemoveScheduleInexistent()
    {
        $this->expectException(ModelNotFound::class);
        $this->sampleModel->removeSchedule(0);
    }

    /**
     * Testa o método que retorna os horaŕios disponiveis hoje.
     *
     * @return void
     */
    public function testAvailableToday()
    {
        $schedule = $this->getSchedule(12, 45);
        $contain = [
            'start_at' => Carbon::parse($schedule->start_at),
            'end_at' => Carbon::parse($schedule->end_at)
        ];

        $this->assertNotContains(Scheduler::availableToday(45, Carbon::today()->addHour(12)), $contain);
    }
}