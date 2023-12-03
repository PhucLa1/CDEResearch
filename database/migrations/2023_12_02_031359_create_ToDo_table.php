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
            $table->integer('UserID');
            $table->integer('FileID');
            $table->string('Title');
            $table->text('Descriptions');
            $table->dateTime('StartDate');
            $table->dateTime('FinishDate');
            $table->tinyInteger('TDStatus');
            $table->tinyInteger('Priorities');
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
