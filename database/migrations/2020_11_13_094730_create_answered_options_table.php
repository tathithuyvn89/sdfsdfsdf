<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnsweredOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answered_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('option_id');
            $table->unsignedBigInteger('answered_quiz_id');
            // $table->tinyInteger('correct');

            $table->timestamps();
            $table->foreign('option_id')
            ->references('id')
            ->on('options')
            ->onDelete('cascade');
            $table->foreign('answered_quiz_id')
            ->references('id')
            ->on('answered_quizzes')
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
        Schema::dropIfExists('answered_options');
    }
}
