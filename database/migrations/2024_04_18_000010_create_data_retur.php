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
        Schema::create('data_retur', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary()
            ->index();
            $table->string('custmrCode', 15)
            ->nullable(true)
            ->index();
            $table->string('custmrName', 200)
            ->nullable(true)
            ->index();
            $table->string('shiptoCode', 10)
            ->nullable(true)
            ->index();
            $table->string('termCode', 5)
            ->nullable(true);
            $table->string('whsCode', 10)
            ->nullable(true);
            $table->string('whsCodeTo', 10)
            ->nullable(true);
            $table->string('refference', 200)
            ->nullable(true);
            $table->string('comments', 200)
            ->nullable(true);
            $table->date('docDate')
            ->nullable(true);
            $table->dateTime('proccessDate')
            ->nullable(true);
            $table->char('transferSts', 1)
            ->default(0)
            ->nullable(true)
            ->comment('0=belum transfer, 1=sudah transfer');
            $table->integer('companyId')
            ->default(1)
            ->nullable(true)
            ->comment('1 MB 4 TPS');
            $table->integer('trxid')
            ->default(1)
            ->nullable(true)
            ->comment('1 Goodsreceipt 2 Goodsissue');
            $table->integer('isllb')
            ->default(0)
            ->nullable(true)
            ->comment('1 LLB 0 Regular');
            $table->integer('docEntryGR1')
            ->default(0)
            ->nullable(true)
            ->comment('base docentry GoodsReceipt1');
            $table->integer('docEntryGI')
            ->default(0)
            ->nullable(true)
            ->comment('base docentry GoodsIssue');
            $table->integer('docEntryGR2')
            ->default(0)
            ->nullable(true)
            ->comment('base docentry GoodsReceipt2');
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('data_returdetail', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary()
            ->index();
            $table->foreignId('baseId')
            ->index()
            ->nullable(true)
            ->references('id')
            ->on('data_retur')
            ->cascadeOnDelete();
            $table->integer('lineNo', false, true)
            ->nullable(true)
            ->index();
            $table->string('itemCodeBase', 100)
            ->nullable(true)
            ->index();
            $table->string('itemCode', 100)
            ->nullable(true)
            ->index();
            $table->integer('quantity', false, true)
            ->nullable(true)
            ->index();
            $table->decimal('disc2', 5, 2)
            ->default(0.00)
            ->nullable(true);
            $table->decimal('disc3', 5, 2)
            ->default(0.00)
            ->nullable(true);
            $table->string('batchNo', 10)
            ->nullable(true);
            $table->date('expireDate')
            ->nullable(true);
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('itemCode')
            ->references('prod_number')
            ->on('product_info_do')
            ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_returdetail');
        Schema::dropIfExists('data_retur');
    }
};
