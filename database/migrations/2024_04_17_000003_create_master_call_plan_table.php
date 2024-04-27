<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_call_plan', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary()
            ->index();
            $table->string('month_plan', 15)
            ->nullable(true)
            ->index();
            $table->string('year_plan', 15)
            ->nullable(true)
            ->index();
            $table->foreignId('user_id')
            ->references('user_id')
            ->on('user_info')
            ->cascadeOnDelete();
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
        Schema::dropIfExists('master_call_plan');
    }
};
