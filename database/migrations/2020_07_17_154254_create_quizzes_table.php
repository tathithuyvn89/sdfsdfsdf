<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title_ja');
            $table->string('title_en');
            $table->text('sentence_ja');
            $table->text('sentence_en');
            $table->string('picture_ja')->nullable();
            $table->string('picture_en')->nullable();
            $table->text('explain_correct_ja');
            $table->text('explain_correct_en');
            $table->text('explain_incorrect_ja');
            $table->text('explain_incorrect_en');
            $table->unsignedBigInteger('admin_created');
            $table->timestamps();

            $table->foreign('admin_created')
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
        Schema::dropIfExists('quizzes');
    }
}
