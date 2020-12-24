<?php

namespace Tests\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use App\Laravue\Models\User;
use App\Laravue\Models\Quiz;
class QuizzTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /** @test */

    public function quizz_database_has_expected_columns()
    {
        $this->assertTrue(
          Schema::hasColumns('quizzes', [
            'title_ja', 'title_en','sentence_ja','sentence_en','picture_ja','picture_en','explain_correct_ja','explain_correct_en','explain_incorrect_ja','explain_incorrect_en','admin_created'
        ]), 1);
    }
    /** @test */
    public function a_quiz_belongs_to_a_user()
    {
        $user = factory(User::class)->create();
        $quizz = factory(Quiz::class)->create(['admin_created' => $user->id]);

        //check instance BelongsTo
        $this->assertInstanceOf(User::class, $quizz->user);
        //check foreignkey is the same or not
        // $this->assertEquals('admin_created', $quizz->user->getForeignKey());
    }
    /** @test */
    public function a_quiz_belongs_to_many_survey()
    {
        factory(\App\Laravue\Models\User::class)->create();
        factory(\App\Laravue\Models\Classs::class)->create();
        factory(\App\Laravue\Models\Department::class)->create();
        $survey = factory(\App\Laravue\Models\Survey::class)->create();
        $quiz = factory(\App\Laravue\Models\Quiz::class)->create();

        // surveys are related to quizzes and is a collection instance.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $quiz->surveys);
    }
    /** @test */
    public function test_Quiz_has_many_Options()
    {
        factory(\App\Laravue\Models\User::class)->create();
        $quiz = factory(\App\Laravue\Models\Quiz::class)->create();
        $option = factory(\App\Laravue\Models\Option::class)->create(['quiz_id' => $quiz->id]);

        // Method 1: A option exists in a quiz's option collections.
        $this->assertTrue($quiz->options->contains($option));

        // Method 2: Count that a quiz options collection exists.
        $this->assertEquals(1, $quiz->options->count());

        // Method 3: options are related to quizzes and is a collection instance.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $quiz->options);
    }
}
