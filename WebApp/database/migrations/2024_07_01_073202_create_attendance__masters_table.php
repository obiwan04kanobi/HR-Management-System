<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_masters', function (Blueprint $table) {
            $table->id("attendance_id");
            $table->unsignedBigInteger('employee_id');
            $table->time('punch_in')->nullable();
            $table->time('punch_out')->nullable();
            $table->date('date')->index(); 
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('employee_id')->references('employee_id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_masters');
    }
}
