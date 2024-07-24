<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('admin_messages', function (Blueprint $table) {
            $table->id('message_id');
            $table->foreignId('attendance_id')->constrained('attendance_masters', 'attendance_id')->onDelete('cascade');
            $table->foreignId('message_from')->constrained('employees', 'employee_id')->onDelete('cascade');
            $table->foreignId('message_to')->constrained('employees', 'employee_id')->onDelete('cascade');
            $table->text('message');
            $table->boolean('message_status');
            $table->foreignId('parent_id')->nullable()->constrained('admin_messages', 'message_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_messages');
    }
}
