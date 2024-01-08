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
        Schema::create('explorer', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100);
            $table->tinyInteger('versions');
            $table->string('note')->nullable();
            $table->integer('parent_id');
            $table->tinyInteger('status')->default(1);
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('user_id');
            $table->string('tag',100)->nullable();
            $table->tinyInteger('type'); //0:file,1:folder
            $table->string('url')->nullable();
            $table->timestamps();

            //foreign key
            //$table->foreign('user_id')->references('id')->on('users');
            //$table->foreign('project_id')->references('id')->on('project');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('explorer');
    }
};
