<?php namespace H4ad\Scheduler\Tests\Unit;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use H4ad\Scheduler\Tests\TestCase;
use H4ad\Scheduler\Models\Schedule;
use H4ad\Scheduler\Exceptions\ModelNotFound;

class ScheduleTest extends TestCase
{
	/**
	 * Testes no método [setStatus] do Schedule.
	 *
	 * @return void
	 */
	public function testSetStatus()
	{
		$schedule = $this->getSchedule(12, 45);
		$status = $this->getScheduleStatus('teste');

		$schedule->setStatus($status->id);
		$this->assertEquals($status->id, $schedule->fresh()->status);

		$statusTwo = $this->getScheduleStatus('teste2');
		$schedule->setStatus($statusTwo->name);
		$this->assertEquals($statusTwo->id, $schedule->status);

		$this->expectException(ModelNotFound::class);
		$schedule->setStatus(0);
	}

	/**
	 * Teste no método [byStartAt].
	 *
	 * @return void
	 */
	public function testByStartAt()
	{
		$schedule = $this->getSchedule(12, 45)->fresh();
		$this->assertEquals($schedule, Schedule::byStartAt($schedule->start_at)->first());
	}
}