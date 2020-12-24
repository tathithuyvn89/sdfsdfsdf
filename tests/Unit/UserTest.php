<?php

// use PHPUnit\Framework\TestCase;
// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Support\Facades\Schema;
namespace Tests\Unit;

use App\Laravue\Models\Quiz;
use App\Laravue\Models\Survey;
use App\Laravue\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /** @test */
    public function user_database_has_expected_columns()
    {
        $this->assertTrue(
          Schema::hasColumns('users', [
            'id','role_id', 'name', 'password'
        ]), 1);
    }
    /** @test */
    public function test_user_has_many_surveys()
    {
        $user = factory(\App\Laravue\Models\User::class)->create();
        factory(\App\Laravue\Models\Department::class)->create();
        factory(\App\Laravue\Models\Classs::class)->create();
        $survey = factory(\App\Laravue\Models\Survey::class)->create(['admin_created' => $user->id]);
        // $survey = factory(Survey::class)->create(['admin_created' => $user->id]);

        // Method 1: A survey exists in a user's survey collections.
        $this->assertTrue($user->surveys->contains($survey));

        // Method 2: Count that a user surveys collection exists.
        $this->assertEquals(1, $user->surveys->count());

        // Method 3: Surveys are related to user and is a collection instance.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->surveys);
    }
    public function test_user_has_many_quizzes()
    {
        $user = factory(\App\Laravue\Models\User::class)->create();
        $quiz = factory(\App\Laravue\Models\Quiz::class)->create(['admin_created' => $user->id]);

        // Method 1: A quiz exists in a user's quiz collections.
        $this->assertTrue($user->quizzes->contains($quiz));

        // Method 2: Count that a user quiz collection exists.
        $this->assertEquals(1, $user->quizzes->count());

        // Method 3: quizzes are related to users and is a collection instance.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->quizzes);
    }
    public function test_user_has_many_answeredsurveys()
    {
        $user = factory(\App\Laravue\Models\User::class)->create();
        factory(\App\Laravue\Models\Department::class)->create();
        factory(\App\Laravue\Models\Classs::class)->create();
        factory(\App\Laravue\Models\Survey::class)->create();
        $answeredsurvey = factory(\App\Laravue\Models\AnsweredSurvey::class)->create(['respondent_id' => $user->id]);

        // Method 1: A answeredsurvey exists in a user's answeredsurvey collections.
        $this->assertTrue($user->answeredsurveys->contains($answeredsurvey));

        // Method 2: Count that a user answeredsurveys collection exists.
        $this->assertEquals(1, $user->answeredsurveys->count());

        // Method 3: answeredsurveys are related to users and is a collection instance.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->answeredsurveys);
    }
}
