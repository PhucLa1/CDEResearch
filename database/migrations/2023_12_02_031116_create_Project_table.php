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
        Schema::create('Project', function (Blueprint $table) {
            $table->id();
            $table->string('ProjectName');
            $table->text('Note');
            $table->string('thumbnails');
            $table->dateTime('StartDate');
            $table->dateTime('FinishDate');
            $table->tinyInteger('Status');
            $table->timestamps();
            $table->Integer('todo_permission')->default(0);
            $table->Integer('invite_permission')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Project');
    }
};
