<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToRoleMasterTable extends Migration
{
    public function up()
    {
        Schema::table('role_master', function (Blueprint $table) {
            $table->foreign('created_by')->references('employee_id')->on('employees')->onDelete('set null');
            $table->foreign('updated_by')->references('employee_id')->on('employees')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('role_master', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });
    }
}
