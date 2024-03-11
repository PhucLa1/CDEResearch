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
        Schema::create('Files', function (Blueprint $table) {
            $table->id();
            $table->string('FileNames',100);
            $table->string('FileType',100);
            $table->integer('Size');
            $table->string('Versions');
            $table->string('Note');
            $table->integer('FolderID');
            $table->tinyInteger('Status');
            $table->integer('FirstVerID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Files');
    }
};
