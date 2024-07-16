<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleMasterTable extends Migration
{
    public function up()
    {
        Schema::create('role_master', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('role', 50)->notNull();
            $table->tinyInteger('active')->default(1);
            $table->timestamp('created_on')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('updated_on')->nullable()->useCurrentOnUpdate();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps(); // Include Laravel's default timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('role_master');
    }
}
