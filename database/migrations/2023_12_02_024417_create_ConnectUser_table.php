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
        Schema::create('ConnectUser', function (Blueprint $table) {
            $table->id();
            $table->integer('UserID');
            $table->integer('GroupUserID');
            $table->integer('KindType');
            $table->tinyInteger('ReadPermission');
            $table->tinyInteger('EditPermission');
            $table->tinyInteger('Status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ConnectUser');
    }
};
