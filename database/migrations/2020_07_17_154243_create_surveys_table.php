<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title_ja');
            $table->string('title_en');
            $table->tinyInteger('display');
            $table->tinyInteger('status');
            $table->string('correct_pic');
            $table->string('incorrect_pic');
            $table->string('completemessage_ja');
            $table->string('completemessage_en');
            $table->integer('department_id')->unsigned();
            $table->integer('classs_id')->unsigned();
            $table->unsignedBigInteger('admin_created');
            $table->timestamps();

            $table->foreign('department_id')
            ->references('id')
            ->on('departments')
            ->onDelete('cascade');
            $table->foreign('classs_id')
            ->references('id')
            ->on('classses')
            ->onDelete('cascade');
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
        Schema::dropIfExists('surveys');
    }
}
