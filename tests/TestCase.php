<?php

namespace Rubik\LaravelInvite\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Rubik\LaravelInvite\LaravelInviteServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Rubik\\LaravelInvite\\Tests\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelInviteServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__ . '/../database/migrations/create_invites_table.php.stub';
        $migration2 = include __DIR__ . '/TestSupport/database/migrations/create_test_models_table.php.stub';
        $migration3 = include __DIR__ . '/TestSupport/database/migrations/create_users_table.php.stub';
        $migration->up();
        $migration2->up();
        $migration3->up();
    }
}
