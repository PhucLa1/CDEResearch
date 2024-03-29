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
        Schema::create('folder', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100);
            $table->integer('parent_id');
            $table->tinyInteger('status')->default(1);
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('user_id');
            $table->string('tag')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folder');
    }
};
