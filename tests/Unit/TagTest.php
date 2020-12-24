<?php

namespace Tests\Unit;

use App\Laravue\Models\Survey;
use App\Laravue\Models\Tag;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
class TagTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function tag_database_has_expected_columns()
    {
        $this->assertTrue(
          Schema::hasColumns('tags', [
            'id', 'name_ja', 'name_en'
        ]), 1);
    }

    /** @test */
    public function a_tag_belongs_to_many_surveys()
    {
        $tag = factory(\App\Laravue\Models\Tag::class)->create();
        factory(\App\Laravue\Models\Department::class)->create();
        factory(\App\Laravue\Models\Classs::class)->create();
        factory(\App\Laravue\Models\User::class)->create();
        $survey = factory(\App\Laravue\Models\Survey::class)->create();

        // surveys are related to tags and is a collection instance.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $tag->surveys);
    }
}
