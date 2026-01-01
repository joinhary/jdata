<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('throttle', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->nullable();
        $table->string('type');
        $table->integer('attempts')->default(0);
        $table->string('ip')->nullable();
        $table->timestamp('created_at')->nullable();
        $table->timestamp('updated_at')->nullable();
    });
}

public function down()
{
    Schema::dropIfExists('throttle');
}

};
