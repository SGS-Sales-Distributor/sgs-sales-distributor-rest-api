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
        Schema::create('store_info_distri', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id', true)
            ->primary()
            ->index();
            $table->string('store_name', 100)
            ->nullable(false)
            ->index();
            $table->string('store_alias', 200)
            ->nullable(false)
            ->index();
            $table->text('store_address')
            ->nullable(false);
            $table->string('store_phone', 20)
            ->unique()
            ->nullable(false)
            ->index();
            $table->string('store_fax', 20)
            ->unique()
            ->nullable(false)
            ->index();
            $table->foreignId('store_type_id')
            ->references('store_type_id')
            ->on('store_type')
            ->cascadeOnDelete();
            $table->foreignId('subcabang_id')
            ->references('id')
            ->on('store_cabang')
            ->cascadeOnDelete();
            $table->string('store_code', 20)
            ->default(0)
            ->nullable(false)
            ->index();
            $table->tinyInteger('active', false, true)
            ->default(1)
            ->nullable(false)
            ->index();
            $table->foreignId('subcabang_idnew')
            ->references('id')
            ->on('store_cabang')
            ->cascadeOnDelete();
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('store_info_distri_person', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary();
            $table->foreignId('store_id')
            ->nullable(true)
            ->index()
            ->references('store_id')
            ->on('store_info_distri')
            ->cascadeOnDelete();
            $table->string('owner', 255)
            ->nullable(true)
            ->index();
            $table->string('nik_owner', 20)
            ->nullable(true)
            ->index();
            $table->string('email_owner', 100)
            ->unique()
            ->nullable(true)
            ->index();
            $table->string('ktp_owner', 255)
            ->nullable(true);
            $table->string('photo_other', 255)
            ->nullable(true);
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
        Schema::dropIfExists('store_info_distri_person');
        Schema::dropIfExists('store_info_distri');
    }
};
