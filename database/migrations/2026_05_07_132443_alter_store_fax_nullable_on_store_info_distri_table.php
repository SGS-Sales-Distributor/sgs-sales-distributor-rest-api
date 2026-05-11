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
        Schema::table('store_info_distri', function (Blueprint $table) {

            $table->dropUnique('store_info_distri_store_fax_unique');
            $table->string('store_fax')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_info_distri', function (Blueprint $table) {

            $table->string('store_fax')->nullable(false)->change();
            $table->unique('store_fax');
        });
    }
};
