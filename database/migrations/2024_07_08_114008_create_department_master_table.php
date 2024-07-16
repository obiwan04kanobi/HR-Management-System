<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentMasterTable extends Migration
{
    public function up()
    {
        // Schema::disableForeignKeyConstraints();
        
        Schema::create('department_master', function (Blueprint $table) {
            $table->id('dept_id');
            $table->string('dept_name', 50);
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        // Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::dropIfExists('department_master');
    }
}
