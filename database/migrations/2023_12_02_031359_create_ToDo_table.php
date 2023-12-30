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
        Schema::create('ToDo', function (Blueprint $table) {
            $table->id();
            $table->string('Name')->nullable();
            $table->integer('AssginTo')->nullable();
            $table->integer('FileID')->nullable();
            $table->string('Title');
            $table->text('Descriptions')->nullable();
            $table->dateTime('StartDate');
            $table->dateTime('FinishDate');
            $table->tinyInteger('TDStatus')->default(0);
            $table->tinyInteger('Priorities')->default(0);
            $table->string('Tag')->nullable();
            $table->integer('ProjectID');
            $table->integer('UserID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ToDo');
    }
};
