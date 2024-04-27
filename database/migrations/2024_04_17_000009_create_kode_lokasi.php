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
        Schema::create('kode_lokasi', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary()
            ->index();
            $table->string('kode_cabang', 255)
            ->unique()
            ->nullable(false)
            ->index();
            $table->string('nama_cabang', 255)
            ->nullable(false)
            ->index();
            $table->string('kode_lokasi', 10)
            ->nullable(true)
            ->index();
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kode_lokasi');
    }
};
