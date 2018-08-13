<?php namespace H4ad\Scheduler\Migrations;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Config::get('scheduler.schedules_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_type');
            $table->integer('model_id');
            $table->timestamp('start_at');
            $table->timestamp('end_at')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(Config::get('scheduler.schedules_table'));
    }
}