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
            Schema::create('mst_customer', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_code', 20);
            $table->string('customer_name', 100);
            $table->string('customer_address', 512);
            $table->string('customer_pos_code', 20)->nullable();
            //$table->string('customer_longitude')->nullable();
            //$table->string('customer_latitude')->nullable();
            //$table->string('schedule_list', 500)->nullable();
            $table->integer('status')->default(0);
            $table->string('prefix', 200)->nullable();
            $table->string('unit_code', 20)->nullable();
            //$table->string('customer_tipe_company')->nullable();
            // $table->tinyInteger('visit_status')->default(0)->nullable();
            //$table->tinyInteger('customer_pusat')->nullable()->default(1);
            // $table->string('customer_category')->nullable()->default(null);
            // $table->string('customer_category_risk')->nullable()->default(null);

            // audit management
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_customer');
    }
};
