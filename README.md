# Laravel Scheduler
<p align="center">
    <a href="https://scrutinizer-ci.com/g/H4ad/laravel-scheduler/build-status/master">
        <img src="https://scrutinizer-ci.com/g/H4ad/laravel-scheduler/badges/build.png?b=master" alt="Build status"/>
    </a>
    <a href="https://scrutinizer-ci.com/g/H4ad/laravel-scheduler/?branch=master">
        <img src="https://scrutinizer-ci.com/g/H4ad/laravel-scheduler/badges/quality-score.png?b=master" alt="Code Quality"/>
    </a>
    <a href="https://scrutinizer-ci.com/code-intelligence">
        <img src="https://scrutinizer-ci.com/g/H4ad/laravel-scheduler/badges/code-intelligence.svg?b=master" alt="Code Intelligence"/>
    </a>
</p>

Biblioteca que facilita a criação de um sistema de agendamento, como por exemplo agendamento de consultas para uma odontologia.

## Como instalar

Use para adicionar este pacote ao seu projeto:

    composer require h4ad/laravel-scheduler

Depois vá em config/app.php e adicione a seguinte linha ao array de providers:

    H4ad\Scheduler\SchedulerServiceProvider::class,
