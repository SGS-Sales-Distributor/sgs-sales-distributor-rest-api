<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     */
    public function up(): void
    {
        Schema::create('outlet_request', function (Blueprint $table) {
            $table->id();
            // data toko
            $table->string('store_name', 100);
            $table->string('store_alias', 200)->nullable();
            $table->text('store_address');
            $table->string('store_phone', 20)->nullable();
            $table->unsignedInteger('store_type_id')->nullable();
            $table->unsignedBigInteger('subcabang_id');

            // data owner
            $table->string('owner', 255);
            $table->string('nik_owner', 20)->nullable();
            $table->string('email_owner', 100)->nullable();

            // data requester (MD)
            $table->unsignedBigInteger('requested_by');
            $table->string('requested_by_name', 200)->nullable();

            // status
            $table->enum('status', ['pending', 'registered', 'rejected'])->default('pending');
            $table->text('notes')->nullable();

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
        Schema::dropIfExists('outlet_request');
    }
};
