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
        Schema::create('explorer_permission', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('explorer_id');
            $table->tinyInteger('permission');
            $table->timestamps();

            // //foreign key
            // $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('explorer_id')->references('id')->on('explorer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('explorer_permission');
    }
};
