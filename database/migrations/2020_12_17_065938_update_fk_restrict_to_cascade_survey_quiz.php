<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFkRestrictToCascadeSurveyQuiz extends Migration
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
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
