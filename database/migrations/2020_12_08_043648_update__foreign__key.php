<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropForeign('surveys_classs_id_foreign');
            $table->dropForeign('surveys_department_id_foreign');
            $table->dropForeign('surveys_admin_created_foreign');

            $table->foreign('department_id')
            ->references('id')
            ->on('departments')
            ->onDelete('restrict');
            $table->foreign('classs_id')
            ->references('id')
            ->on('classses')
            ->onDelete('restrict');
            $table->foreign('admin_created')
            ->references('id')
            ->on('users')
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
        Schema::table('surveys', function (Blueprint $table) {
            //
        });
    }
}
