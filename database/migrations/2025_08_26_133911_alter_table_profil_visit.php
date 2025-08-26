<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('profil_visit', function (Blueprint $table) {
            // ubah panjang kolom (contoh: token)
            $table->text('ket')->nullable()->change();
            $table->text('comment_appr')->nullable()->change();
            // atau kalau cuma butuh varchar lebih panjang:
            // $table->string('token', 1000)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_visit', function (Blueprint $table) {
            // kembalikan ke varchar(255)
            $table->string('ket', 255)->nullable()->change();
            $table->string('comment_appr', 255)->nullable()->change();
        });
    }
};
