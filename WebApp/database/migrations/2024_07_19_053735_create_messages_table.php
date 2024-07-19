<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id('message_id');
            $table->unsignedBigInteger('attendance_id');
            $table->unsignedBigInteger('message_from');
            $table->unsignedBigInteger('message_to');
            $table->string('message')->nullable();  // New column for messages
            $table->BigInteger('message_status')->nullable();          // 0 means cleared, 1 not read, 2 means read
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('message_from')->references('employee_id')->on('employees');
            $table->foreign('message_to')->references('employee_id')->on('employees');
            $table->foreign('attendance_id')->references('attendance_id')->on('attendance_masters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
