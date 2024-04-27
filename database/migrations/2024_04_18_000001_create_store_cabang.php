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
        Schema::create('store_cabang', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary();
            $table->foreignId('province_id')
            ->nullable(true)
            ->index()
            ->references('id_province')
            ->on('master_province')
            ->cascadeOnDelete();
            $table->string('kode_cabang', 10)
            ->nullable(true)
            ->index();
            $table->string('nama_cabang', 200)
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
        Schema::dropIfExists('store_cabang');
    }
};
