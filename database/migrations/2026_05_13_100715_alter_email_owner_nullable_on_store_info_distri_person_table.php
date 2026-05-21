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
        Schema::table('store_info_distri_person', function (Blueprint $table) {
            $table->dropUnique('store_info_distri_person_email_owner_unique');
            $table->string('email_owner', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_info_distri_person', function (Blueprint $table) {
              $table->string('email_owner', 100)->nullable(false)->change();
            $table->unique('email_owner');
        });
    }
};