<?php

namespace Tests\Unit;

use App\Laravue\Models\Survey;
use App\Laravue\Models\Tag;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;

class AnsweredSurvey extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    /**
     * @test
     */

    public function quizz_database_has_expected_columns()
    {
        $this->assertTrue(
          Schema::hasColumns('answered_surveys', [
            'id','respondent_id','survey_id','result','created_at','updated_at'
        ]), 1);
    }
    /** @test */
    public function a_answerdsurvey_belongs_to_a_user()
    {
        factory(\App\Laravue\Models\Classs::class)->create();
        factory(\App\Laravue\Models\Department::class)->create();
        $user = factory(\App\Laravue\Models\User::class)->create();
        factory(\App\Laravue\Models\Survey::class)->create();
        $answeredsurvey = factory(\App\Laravue\Models\AnsweredSurvey::class)->create(['respondent_id' => $user->id]);

        //check instance BelongsTo
        $this->assertInstanceOf(\App\Laravue\Models\User::class, $answeredsurvey->user);
        //check foreignkey is the same or not
        // error_log("foreignkey = " +$answeredsurvey->user->getForeignKey());
        // $this->assertEquals('respondent_id', $answeredsurvey->user()->getForeignKey());
    }
}
