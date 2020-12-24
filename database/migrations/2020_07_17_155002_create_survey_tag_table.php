<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_tag', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('survey_id');
            $table->Integer('tag_id')->unsigned();
            // $table->timestamps();

            $table->foreign('survey_id')
            ->references('id')
            ->on('surveys')
            ->onDelete('cascade');

            $table->foreign('tag_id')
            ->references('id')
            ->on('tags')
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
        Schema::dropIfExists('survey_tag');
    }
}
