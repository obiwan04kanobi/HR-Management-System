<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('employees', function (Blueprint $table) {
            $table->id('employee_id')->unsignedBigInteger();
            $table->string('name', 100);
            $table->unsignedBigInteger('department');
            $table->unsignedBigInteger('designation');
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('report_to')->nullable();
            $table->date('date_join');
            $table->boolean('status')->default(true);

            // Constraints
            $table->foreign('department')->references('dept_id')->on('department_master');
            $table->foreign('designation')->references('role_id')->on('role_master');
            $table->foreign('report_to')->references('employee_id')->on('employees')->onDelete('set null');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
