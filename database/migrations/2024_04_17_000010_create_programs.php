<?php

use Carbon\Carbon;
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
        Schema::create('program', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary();
            $table->foreignId('id_type_program')
            ->index()
            ->nullable(false)
            ->references('id_type')
            ->on('master_type_program')
            ->cascadeOnDelete();
            $table->string('name_program', 255)
            ->nullable(false)
            ->index();
            $table->string('keterangan', 255)
            ->nullable(true);
            $table->integer('active', false, true)
            ->nullable(false)
            ->index();
            $table->date('periode_start')
            ->default(Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d'))
            ->nullable(true);
            $table->date('periode_end')
            ->default(Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d'))
            ->nullable(true);
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('program_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary()
            ->index();
            $table->foreignId('id_program')
            ->index()
            ->nullable(false)
            ->references('id')
            ->on('program')
            ->cascadeOnDelete();
            $table->string('condition', 255)
            ->nullable(true)
            ->index();
            $table->string('get', 255)
            ->nullable(true)
            ->index();
            $table->string('product', 255)
            ->nullable(true)
            ->index();
            $table->integer('qty', false, true)
            ->nullable(true)
            ->index();
            $table->double('disc_val')
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
        Schema::dropIfExists('program_detail');
        Schema::dropIfExists('program');
    }
};
