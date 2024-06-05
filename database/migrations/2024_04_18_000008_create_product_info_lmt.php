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
        Schema::create('product_info_lmt', function (Blueprint $table) {
            $table->string('prod_number', 100)
            ->nullable(false)
            ->index();
            $table->foreign('prod_number')
            ->references('prod_number')
            ->on('product_info_do')
            ->cascadeOnDelete();
            $table->primary(['prod_number']);
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
        Schema::dropIfExists('product_info_lmt');
    }
};
