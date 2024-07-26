<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompoffTable20240724 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('compoff_master', function (Blueprint $table) {
            $table->id('compoff_id');
            $table->unsignedBigInteger('holiday_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->date('compoff_taken_date')->nullable();
            $table->string('remarks', 255)->nullable();   // Reason for leave
            $table->boolean('status'); 
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('employee_id')->references('employee_id')->on('employees');
            $table->foreign('date')->references('date')->on('attendance_masters');
            $table->foreign('holiday_id')->references('holiday_id')->on('holidays_masters');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compoff_master');
    }
};

