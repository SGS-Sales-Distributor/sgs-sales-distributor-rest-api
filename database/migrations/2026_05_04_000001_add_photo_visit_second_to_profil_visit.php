<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profil_visit', function (Blueprint $table) {
            $table->text('photo_visit_in_second')->nullable();
            $table->text('photo_visit_out_second')->nullable();
            $table->text('keterangan_out')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('profil_visit', function (Blueprint $table) {
            $table->dropColumn('photo_visit_in_second');
            $table->dropColumn('photo_visit_out_second');
            $table->dropColumn('keterangan_out');
        });
    }
};
