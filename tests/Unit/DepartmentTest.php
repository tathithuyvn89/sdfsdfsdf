<?php

namespace Tests\Unit;
use App\Laravue\Models\Survey;
use App\Laravue\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /** @test */
    public function department_database_has_expected_columns()
    {
        $this->assertTrue(
          Schema::hasColumns('departments', [
            'id', 'name_ja', 'name_en'
        ]), 1);
    }
    /** @test */
    public function test_department_has_many_surveys()
    {

        $department = factory(\App\Laravue\Models\Department::class)->create();
        factory(\App\Laravue\Models\User::class)->create();
        factory(\App\Laravue\Models\Classs::class)->create();
        $survey = factory(\App\Laravue\Models\Survey::class)->create(['department_id' => $department->id]);

        // Method 1: A survey exists in a department's survey collections.
        $this->assertTrue($department->surveys->contains($survey));

        // Method 2: Count that a department surveys collection exists.
        $this->assertEquals(1, $department->surveys->count());

        // Method 3: surveys are related to departments and is a collection instance.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $department->surveys);
    }
}
