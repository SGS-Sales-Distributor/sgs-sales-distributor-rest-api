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
        Schema::create('master_call_plan_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary()
            ->index();
            $table->foreignId('call_plan_id')
            ->references('id')
            ->on('master_call_plan')
            ->cascadeOnDelete();         
            $table->foreignId('store_id')
            ->references('store_id')
            ->on('store_info_distri')
            ->cascadeOnDelete();
            $table->date('date')
            ->default(Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d'))
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
        Schema::dropIfExists('master_call_plan_detail');
    }
};
