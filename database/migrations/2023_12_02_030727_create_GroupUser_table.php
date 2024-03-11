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
        Schema::create('GroupUser', function (Blueprint $table) {
            $table->id();
            $table->string('GUName');
            $table->string('Unit');
            $table->string('Purpose');
            $table->integer('ProjectID');
            $table->integer('Status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('GroupUser');
    }
};
