<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SurveyTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /** @test */

    public function survey_database_has_expected_columns()
    {
        $this->assertTrue(
          Schema::hasColumns('surveys', [
            'title_ja', 'title_en','display','correct_pic','incorrect_pic', 'completemessage_ja','completemessage_en','department_id','classs_id','admin_created'
        ]), 1);
    }
    /** @test */
    public function a_survey_belongs_to_a_classs()
    {
        factory(\App\Laravue\Models\User::class)->create();
        factory(\App\Laravue\Models\Department::class)->create();
        $classs = factory(\App\Laravue\Models\Classs::class)->create();
        $survey = factory(\App\Laravue\Models\Survey::class)->create(['classs_id' => $classs->id]);

        //check instance BelongsTo
        $this->assertInstanceOf(\App\Laravue\Models\Classs::class, $survey->classs);
        //check foreignkey is the same or not
        $this->assertEquals('classs_id', $survey->classs->getForeignKey());
    }
    /** @test */
    public function a_survey_belongs_to_a_department()
    {
        factory(\App\Laravue\Models\User::class)->create();
        $department = factory(\App\Laravue\Models\Department::class)->create();
        factory(\App\Laravue\Models\Classs::class)->create();
        $survey = factory(\App\Laravue\Models\Survey::class)->create(['classs_id' => $department->id]);

        //check instance BelongsTo
        $this->assertInstanceOf(\App\Laravue\Models\Department::class, $survey->department);
        //check foreignkey is the same or not
        $this->assertEquals('department_id', $survey->department->getForeignKey());
    }
    /** @test */
    public function a_survey_belongs_to_a_user()
    {
        $user = factory(\App\Laravue\Models\User::class)->create();
        factory(\App\Laravue\Models\Department::class)->create();
        factory(\App\Laravue\Models\Classs::class)->create();
        $survey = factory(\App\Laravue\Models\Survey::class)->create(['admin_created' => $user->id]);

        //check instance BelongsTo
        $this->assertInstanceOf(\App\Laravue\Models\User::class, $survey->user);
        //check foreignkey is the same or not
        // $this->assertEquals('admin_created', $survey->user->getForeignKey());
    }
    /** @test */
    public function a_surveys_belongs_to_many_tags()
    {
        $tag = factory(\App\Laravue\Models\Tag::class)->create();
        factory(\App\Laravue\Models\Department::class)->create();
        factory(\App\Laravue\Models\Classs::class)->create();
        factory(\App\Laravue\Models\User::class)->create();
        $survey = factory(\App\Laravue\Models\Survey::class)->create();

        // tags are related to surveys and is a collection instance.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $survey->tags);
    }
}
