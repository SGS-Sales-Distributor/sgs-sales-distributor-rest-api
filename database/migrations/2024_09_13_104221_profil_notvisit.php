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
        Schema::create('profil_notvisit',function (Blueprint $table){
            $table->unsignedBigInteger('id',true)->primary()->index();
            $table->foreignId('id_master_call_plan_detail')->nullable(true)->references('id')->on('master_call_plan_detail')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('ket',255)->nullable(true);
            $table->string('created_by',255)->nullable(true);
            $table->string('updated_by',255)->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_notvisit');
    }
};
