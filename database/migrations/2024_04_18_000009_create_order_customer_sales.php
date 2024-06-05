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
        Schema::create('order_customer_sales', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary()
            ->index();
            $table->string('no_order', 100)
            ->nullable(true)
            ->index();
            $table->date('tgl_order')
            ->nullable(true);
            $table->enum('tipe', ['SO', 'LLB'])
            ->default('SO')
            ->nullable(true);
            $table->integer('company')
            ->nullable(true)
            ->index();
            $table->char('top', 2)
            ->nullable(true)
            ->index();
            $table->string('cust_code', 50)
            ->nullable(true)
            ->index();
            $table->string('ship_code', 50)
            ->nullable(true)
            ->index();
            $table->string('whs_code', 20)
            ->nullable(true);
            $table->string('whs_code_to', 20)
            ->nullable(true);
            $table->string('order_sts', 10)
            ->nullable(true)
            ->index()
            ->comment('Open, Complete, Close, Draft');
            $table->integer('totOrderQty')
            ->nullable(true)
            ->index();
            $table->integer('totReleaseQty')
            ->nullable(true);
            $table->string('keterangan', 255)
            ->nullable(true);
            $table->string('llb_gabungan_reff', 255)
            ->nullable(true);
            $table->string('llb_gabungan_sts', 20)
            ->default('Open')
            ->nullable(true)
            ->comment('Open, Transfered');
            $table->dateTime('uploaded_at')
            ->nullable(true);
            $table->string('uploaded_by', 50)
            ->nullable(true);
            $table->foreignId('store_id')
            ->nullable(false)
            ->index()
            ->references('store_id')
            ->on('store_info_distri')
            ->cascadeOnDelete();
            $table->foreignId('status_id')
            ->nullable(true)
            ->index()
            ->references('id')
            ->on('master_status')
            ->cascadeOnDelete();
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_customer_sales_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary()
            ->index();
            $table->foreignId('orderId')
            ->nullable(true)
            ->index()
            ->references('id')
            ->on('order_customer_sales')
            ->cascadeOnDelete();
            $table->integer('lineNo')
            ->nullable(true);
            $table->string('itemCodeCust', 100)
            ->nullable(true)
            ->index();
            $table->string('itemCode', 100)
            ->nullable(true)
            ->index();
            $table->integer('qtyOrder')
            ->nullable(true);
            $table->integer('releaseOrder')
            ->nullable(true);
            $table->double('add_disc_1')
            ->nullable(true)
            ->index();
            $table->double('add_disc_2')
            ->nullable(true)
            ->index();
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('itemCodeCust')
            ->references('prod_number')
            ->on('product_info_lmt')
            ->cascadeOnDelete();
            $table->foreign('itemCode')
            ->references('prod_number')
            ->on('product_info_lmt')
            ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_customer_sales_detail');
        Schema::dropIfExists('order_customer_sales');
    }
};
