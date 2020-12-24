<?php

namespace Tests\Unit;

use App\Laravue\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OptionTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /** @test */
    public function tag_database_has_expected_columns()
    {
        $this->assertTrue(
          Schema::hasColumns('options', [
            'id', 'quiz_id', 'option_ja','option_en','correct','position'
        ]), 1);
    }

    /** @test */
    public function a_option_belongs_to_a_quiz()
    {
        factory(\App\Laravue\Models\User::class)->create();
        $quizz = factory(\App\Laravue\Models\Quiz::class)->create();
        $option = factory(\App\Laravue\Models\Option::class)->create(['quiz_id' => $quizz->id]);

        //check instance BelongsTo
        $this->assertInstanceOf(\App\Laravue\Models\Quiz::class, $option->quizz);
        //check foreignkey is the same or not
        $this->assertEquals('quiz_id', $option->quizz->getForeignKey());
    }
}
