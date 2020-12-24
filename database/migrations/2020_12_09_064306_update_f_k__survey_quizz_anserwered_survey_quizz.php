<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFKSurveyQuizzAnserweredSurveyQuizz extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('survey_quiz', function (Blueprint $table) {
            $table->dropForeign('survey_quiz_survey_id_foreign');
            $table->foreign('survey_id')
            ->references('id')
            ->on('surveys')
            ->onDelete('restrict');
        });
        Schema::table('answered_quizzes', function (Blueprint $table) {
            $table->dropForeign('answered_quizzes_answered_survey_id_foreign');
            $table->foreign('answered_survey_id')
            ->references('id')
            ->on('answered_surveys')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
