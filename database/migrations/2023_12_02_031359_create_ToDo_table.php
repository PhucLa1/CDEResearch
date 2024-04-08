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
        Schema::create('todo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('assgin_to')->nullable();
            $table->unsignedInteger('files_id')->nullable();
            $table->string('title');
            $table->text('descriptions')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('finish_date');
            $table->tinyInteger('priorities')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->string('tag')->nullable();
            $table->unsignedInteger('project_id');
            $table->timestamps();

            //foreign key
            // $table->foreign('project_id')->references('id')->on('project');
            // $table->foreign('explorer_id')->references('id')->on('explorer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todo');
    }
};
