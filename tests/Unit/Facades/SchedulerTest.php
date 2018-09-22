<?php namespace H4ad\Scheduler\Tests\Unit\Facades;

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
use H4ad\Scheduler\Exceptions\IntInvalidArgument;

class SchedulerTest extends TestCase
{
    /**
     * Testa o método de transformar para uma instancia em carbon.
     *
     * @return void
     */
    public function testParseToCarbon()
    {
        $this->assertEquals(true, Scheduler::parseToCarbon(Carbon::today()) instanceof Carbon);
        $this->assertEquals(Carbon::parse('2018-07-11'), Scheduler::parseToCarbon('2018-07-11'));
        $this->assertEquals(Carbon::today()->addMinutes(45), Scheduler::parseToCarbon(45, Carbon::today()));

        $this->expectException(IntInvalidArgument::class);
        Scheduler::parseToCarbon(45);
    }

    /**
     * Testa o método de transformar para uma instancia do Schedule.
     *
     * @return void
     */
    public function testParseToSchedule()
    {
        $schedule = $this->sampleModel->addSchedule(Carbon::today(), Carbon::today()->addMinutes(45));

        $this->assertEquals($schedule->id, Scheduler::parseToSchedule($schedule->id)->id);
        $this->assertEquals($schedule->id, Scheduler::parseToSchedule($schedule->start_at)->id);
        $this->assertEquals($schedule->id, Scheduler::parseToSchedule(Carbon::parse($schedule->start_at))->id);
        $this->assertEquals(null, Scheduler::parseToSchedule('2012-12-12'));
    }

    /**
     * Testa o método de init.
     *
     * @return void
     */
    public function testInit()
    {
        Scheduler::init(function ($scheduler) {
            $scheduler->setModelType(self::class);
        });

        $this->assertEquals(self::class, Scheduler::getModelType());
    }
}