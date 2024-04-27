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
        Schema::create('master_target_noo', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary()
            ->index();
            $table->string('usernumber', 30)
            ->nullable(true)
            ->index();
            $table->integer('target', false, true)
            ->default(0)
            ->nullable(false)
            ->index();
            $table->integer('month', false, true)
            ->nullable(true)
            ->index();
            $table->integer('year', false, true)
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
        Schema::dropIfExists('master_target_noo');
    }
};
