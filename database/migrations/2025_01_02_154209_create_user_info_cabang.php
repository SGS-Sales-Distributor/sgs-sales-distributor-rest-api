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
        Schema::create('user_info_cabang', function (Blueprint $table) {
            $table->unsignedBigInteger('id',true)->primary()->index();
            $table->foreignId('user_id')->nullable(true)->references('user_id')->on('user_info')->cascadeOnUpdate();
            $table->integer('cabang_id',false,true)->nullable(true)->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_info_cabang');
    }
};
