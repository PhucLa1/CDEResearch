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
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->tinyInteger('versions');
            $table->string('note')->nullable();
            $table->unsignedInteger('folder_id');
            $table->tinyInteger('status')->default(1);
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('user_id');
            $table->string('tag',100)->nullable();
            $table->string('url')->nullable();
            $table->double('size');
            $table->integer('first_version');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
