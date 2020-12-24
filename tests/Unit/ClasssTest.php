<?php

namespace Tests\Unit;
use App\Laravue\Models\Survey;
use App\Laravue\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ClasssTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /** @test */
    public function classs_database_has_expected_columns()
    {
        $this->assertTrue(
          Schema::hasColumns('classses', [
            'id', 'name_ja', 'name_en'
        ]), 1);
    }
    /** @test */
    public function test_classs_has_many_surveys()
    {

        $classs = factory(\App\Laravue\Models\Classs::class)->create();
        factory(\App\Laravue\Models\User::class)->create();
        factory(\App\Laravue\Models\Department::class)->create();
        $survey = factory(\App\Laravue\Models\Survey::class)->create(['classs_id' => $classs->id]);

        // Method 1: A survey exists in a classs's survey collections.
        $this->assertTrue($classs->surveys->contains($survey));

        // Method 2: Count that a classs surveys collection exists.
        $this->assertEquals(1, $classs->surveys->count());

        // Method 3: surveys are related to classss and is a collection instance.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $classs->surveys);
    }
}
