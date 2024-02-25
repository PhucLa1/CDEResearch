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
        Schema::create('export', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->text('note');
            $table->unsignedInteger('files_id');
            $table->timestamps();

            // //foreign key
            // $table->foreign('explorer_id')->references('id')->on('explorer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export');
    }
};
