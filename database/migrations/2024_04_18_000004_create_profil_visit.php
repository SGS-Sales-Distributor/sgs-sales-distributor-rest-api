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
        Schema::create('profil_visit', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary();
            $table->foreignId('store_id')
            ->nullable(true)
            ->references('store_id')
            ->on('store_info_distri')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->foreignId('user')
            ->references('user_id')
            ->on('user_info')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->text('photo_visit')
            ->nullable(true);
            $table->text('photo_visit_out')
            ->nullable(true);
            $table->date('tanggal_visit')
            ->nullable(true)
            ->index();
            $table->time('time_in')
            ->nullable(true);
            $table->time('time_out')
            ->nullable(true);
            $table->string('purchase_order_in', 255)
            ->nullable(true);
            $table->string('condit_owner', 255)
            ->default('Tidak Ada')
            ->nullable(true)
            ->comment('Ada / Tidak Ada');
            $table->string('ket', 255)
            ->nullable(true);
            $table->string('comment_appr', 255)
            ->nullable(true);
            $table->string('lat_in', 100)
            ->nullable(true);
            $table->string('long_in', 100)
            ->nullable(true);
            $table->string('lat_out', 100)
            ->nullable(true);
            $table->string('long_out', 100)
            ->nullable(true);
            $table->integer('approval', false, true)
            ->default(0)
            ->nullable(true)
            ->comment('Approval=1,NotApproval=0');
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
        Schema::dropIfExists('profil_visit');
    }
};
