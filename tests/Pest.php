<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Rubik\LaravelInvite\Tests\TestCase;
use Rubik\LaravelInvite\Tests\TestSupport\Models\TestModel;

uses(TestCase::class, RefreshDatabase::class)->in(__DIR__);


/**
 *
 * Helper function to create a test model
 *
 * @param string $model
 * @return TestModel|null
 */
function createTestModel(string $model = TestModel::class): ?TestModel
{
    $id = TestModel::factory()->create()->id;

    return $model::find($id);
}
