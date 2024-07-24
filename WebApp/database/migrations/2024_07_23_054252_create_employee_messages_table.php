<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('employee_messages', function (Blueprint $table) {
            $table->id('employee_message_id');
            $table->foreignId('attendance_id')->constrained('attendance_masters', 'attendance_id')->onDelete('cascade');
            $table->foreignId('message_from')->constrained('employees', 'employee_id')->onDelete('cascade');
            $table->foreignId('message_to')->constrained('employees', 'employee_id')->onDelete('cascade');
            $table->text('message');
            $table->boolean('message_status');
            $table->foreignId('parent_id')->nullable()->constrained('employee_messages', 'employee_message_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_messages');
    }
}
