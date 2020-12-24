<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnsweredSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answered_surveys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('respondent_id');
            $table->unsignedBigInteger('survey_id');
            $table->tinyInteger('result');
            $table->integer('current_result');
            $table->integer('total_quizzes');
            $table->timestamps();


            $table->foreign('survey_id')
            ->references('id')
            ->on('surveys')
            ->onDelete('cascade');
            $table->foreign('respondent_id')
            ->references('id')
            ->on('users')
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
        Schema::dropIfExists('answered_surveys');
    }
}
