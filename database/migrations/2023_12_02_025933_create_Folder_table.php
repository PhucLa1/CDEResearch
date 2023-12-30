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
        Schema::create('Folder', function (Blueprint $table) {
            $table->id();
            $table->string('FolderName');
            $table->integer('ParentID');
            $table->tinyInteger('Status')->default(1);
            $table->integer('ProjectID');
            $table->integer('UserID');
            $table->string('Tag',100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Folder');
    }
};
