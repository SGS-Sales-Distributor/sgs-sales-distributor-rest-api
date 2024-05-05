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
        Schema::create('product_status', function (Blueprint $table) {
            $table->unsignedBigInteger('product_status_id', true)
            ->primary()
            ->index();
            $table->string('product_status_name', 255)
            ->nullable(false)
            ->index();
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_type', function (Blueprint $table) {
            $table->unsignedBigInteger('prod_type_id', true)
            ->primary()
            ->index();
            $table->string('prod_type_name', 255)
            ->nullable(false)
            ->index();
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('product_info_do', function (Blueprint $table) {
            $table->string('prod_number', 100)
            ->index()
            ->primary();
            $table->string('prod_barcode_number', 30)
            ->unique()
            ->nullable(false);
            $table->string('prod_universal_number', 30)
            ->unique()
            ->nullable(false);
            $table->string('prod_name', 100)
            ->nullable(false);
            $table->string('prod_base_price', 20)
            ->nullable(false);
            $table->string('prod_unit_price', 20)
            ->nullable(false)
            ->index();
            $table->string('prod_promo_price', 20)
            ->nullable(false);
            $table->string('prod_special_offer', 20)
            ->nullable(false);
            $table->string('prod_special_offer_unit', 20)
            ->nullable(false);
            $table->string('brand_id', 10)
            ->nullable(false)
            ->index();
            $table->string('category_id', 10)
            ->nullable(false)
            ->index();
            $table->integer('category_sub_id')
            ->nullable(false);
            $table->foreignId('prod_type_id')
            ->nullable(true)
            ->index()
            ->references('prod_type_id')
            ->on('product_type')
            ->cascadeOnDelete();
            $table->string('supplier_id', 10)
            ->nullable(true);
            $table->foreignId('prod_status_id')
            ->nullable(true)
            ->index()
            ->references('product_status_id')
            ->on('product_status')
            ->cascadeOnDelete();
            $table->char('status_aktif')
            ->nullable(false);
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('brand_id')
            ->references('brand_id')
            ->on('brand')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_info_do');
        Schema::dropIfExists('product_type');
        Schema::dropIfExists('product_status');
    }
};
