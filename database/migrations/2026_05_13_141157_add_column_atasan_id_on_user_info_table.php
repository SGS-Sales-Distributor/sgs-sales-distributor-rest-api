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
        Schema::table('user_info', function (Blueprint $table) {
            $table->unsignedBigInteger('atasan_id')->nullable();
            // $table->foreign('atasan_id')->references('user_id')->on('user_info')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_info', function (Blueprint $table) {
            // $table->dropForeign(['atasan_id']);
            $table->dropColumn('atasan_id');
        });
    }
};
