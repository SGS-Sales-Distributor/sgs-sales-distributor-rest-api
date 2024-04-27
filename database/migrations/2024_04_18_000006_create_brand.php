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
        Schema::create('brand', function (Blueprint $table) {
            $table->string('brand_id', 10)
            ->index()
            ->nullable(false);
            $table->string('brand_name', 255)
            ->nullable(false)
            ->index();
            $table->integer('status')
            ->nullable(false);
            $table->string('brand_group_id', 10)
            ->nullable(true)
            ->index();
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('brand_group_id')
            ->references('brand_group_id')
            ->on('brand_group')
            ->cascadeOnDelete();
            $table->primary([
                'brand_id',
                'status',
            ], 'brand_id_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand');
    }
};
