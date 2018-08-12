<?php namespace H4ad\Scheduler\Tests;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Illuminate\Support\Facades\Config;
use H4ad\Scheduler\Models\ScheduleStatus;
use Illuminate\Database\Schema\Blueprint;
use H4ad\Scheduler\Tests\Unit\SampleModel;
use Orchestra\Testbench\TestCase as OrchestralTestCase;

class TestCase extends OrchestralTestCase
{
    /**
     * Model usada de exemplo para testar os métodos da Trait.
     *
     * @var SampleModel
     */
    protected $sampleModel;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->runTestMigrations();

        $this->sampleModel = SampleModel::create();
    }

    /**
     * Executa os migrations das tabelas do pacote para os testes.
     *
     * @return void
     */
    private function runTestMigrations()
    {
        $schema = $this->app['db']->connection()->getSchemaBuilder();

        $schema->dropIfExists(Config::get('scheduler.schedules_table'));
        $schema->create(Config::get('scheduler.schedules_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_type');
            $table->integer('model_id');
            $table->timestamp('start_at');
            $table->timestamp('end_at')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $schema->dropIfExists(Config::get('scheduler.schedule_status_table'));
        $schema->create(Config::get('scheduler.schedule_status_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $schema->dropIfExists('sample_models');
        $schema->create('sample_models', function (Blueprint $table) {
            $table->increments('id');
        });

        $schema->dropIfExists('sample_model_fake');
        $schema->create('sample_model_fake', function (Blueprint $table) {
            $table->increments('id');
        });
    }

    /**
     * Define as variáveis do ambiente.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
    }

    /**
     * Get package providers.  At a minimum this is the package being tested, but also
     * would include packages upon which our package depends, e.g. Cartalyst/Sentry
     * In a normal app environment these would be added to the 'providers' array in
     * the config/app.php file.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \H4ad\Scheduler\SchedulerServiceProvider::class
        ];
    }

    /**
     * Get package aliases.  In a normal app environment these would be added to
     * the 'aliases' array in the config/app.php file.  If your package exposes an
     * aliased facade, you should add the alias here, along with aliases for
     * facades upon which your package depends, e.g. Cartalyst/Sentry.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Scheduler' => \H4ad\Scheduler\Facades\Scheduler::class
        ];
    }

    /**
     * Registra e retorna um status de um horário.
     * Estou usando esse método porque quando uso a função [create] ela dá erro.
     *
     * @param  string $name Nome do status
     * @return \H4ad\Scheduler\Models\ScheduleStatus
     */
    protected function getScheduleStatus($name = null)
    {
        $status = new ScheduleStatus;
        $status->name = $name ?? str_random(10);
        $status->save();
        return $status;
    }

    /**
     * Método adicionado apenas para ele não ficar exibindo um Warning para este arquivo
     * mesmo depois de ter configurado para não ler este arquivo.
     *
     * @return void
     */
    public function testDontCry()
    {
        $this->assertEquals(true, true);
    }
}
