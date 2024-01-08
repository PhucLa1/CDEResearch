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
        Schema::table('export', function (Blueprint $table) {
            $table->foreign('explorer_id')->references('id')->on('explorer')->onDelete('CASCADE');
        });
        Schema::table('explorer_permission', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreign('explorer_id')->references('id')->on('explorer')->onDelete('CASCADE');
        });
        Schema::table('explorer', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreign('project_id')->references('id')->on('project')->onDelete('CASCADE');
        });
        Schema::table('tag', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('project')->onDelete('CASCADE');
        });
        Schema::table('todo', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('project')->onDelete('CASCADE');
            $table->foreign('explorer_id')->references('id')->on('explorer')->onDelete('CASCADE');
        });
        Schema::table('user_project', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreign('project_id')->references('id')->on('project')->onDelete('CASCADE');
        });
        Schema::table('comment', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
