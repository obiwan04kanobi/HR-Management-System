<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leave_application_masters', function (Blueprint $table) {
            $table->id('application_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('from_date'); 
            $table->date('to_date');
            $table->unsignedBigInteger('leave_type');
            $table->string('session', 50);      // Half Day, Short Leave or Full day
            $table->string('half', 50)->nullable();      // 1st Half or 2nd Half
            $table->string('remarks', 255);   // Reason for leave
            $table->boolean('status');          // 0 means still pending, 1 means approved
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('employee_id')->references('employee_id')->on('employees');
            $table->foreign('leave_type')->references('leave_id')->on('leave_masters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_application_masters');
    }
};
