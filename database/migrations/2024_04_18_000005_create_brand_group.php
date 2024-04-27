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
        Schema::create('brand_group', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary()
            ->nullable(false)
            ->index();
            $table->string('brand_group_id', 10)
            ->nullable(true)
            ->index();
            $table->string('brand_group_name', 20)
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
        Schema::dropIfExists('brand_group');
    }
};
