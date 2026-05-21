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
        Schema::table('outlet_request', function (Blueprint $table) {
            $table->string('approved_by_name', 200)->nullable();
            $table->string('rejected_by_name', 200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outlet_request', function (Blueprint $table) {
            $table->dropColumn(['approved_by_name', 'rejected_by_name']);
        });
    }
};
