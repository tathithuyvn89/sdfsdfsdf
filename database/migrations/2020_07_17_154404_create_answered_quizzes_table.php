<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnsweredQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answered_quizzes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('quiz_id');
            $table->unsignedBigInteger('answered_survey_id');
            $table->Integer('times')->unsigned();
            $table->tinyInteger('correct');
            $table->timestamps();
            $table->foreign('quiz_id')
            ->references('id')
            ->on('quizzes')
            ->onDelete('restrict');
            $table->foreign('answered_survey_id')
            ->references('id')
            ->on('answered_surveys')
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
        Schema::dropIfExists('answered_quizzes');
    }
}
