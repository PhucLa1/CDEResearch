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
        Schema::create('project', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('note')->nullable();
            $table->string('thumbnails')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('finish_date');
            $table->integer('user_id');
            $table->timestamps();
            $table->tinyInteger('todo_permission')->default(0);
            $table->tinyInteger('invite_permission')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project');
    }
};
