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
    Schema::create('reminders', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->unsigned();
        $table->string('code');
        $table->timestamp('completed_at')->nullable();
        $table->timestamps();

        $table->index('user_id');
        $table->index('code');
    });
}

public function down()
{
    Schema::dropIfExists('reminders');
}

};
