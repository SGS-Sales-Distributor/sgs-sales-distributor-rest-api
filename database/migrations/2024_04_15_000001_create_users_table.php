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
        Schema::create('user_type', function (Blueprint $table) {
            $table->unsignedBigInteger('user_type_id', true)
            ->primary();
            $table->string('user_type_name', 255)
            ->nullable(false)
            ->index();
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_status', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary();
            $table->string('status', 255)
            ->nullable(false)
            ->index();
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_info', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id', true)
            ->primary();
            $table->string('number', 10)
            ->nullable(false)
            ->index();
            $table->string('nik', 20)
            ->unique()
            ->nullable(true);
            $table->string('fullname', 200)
            ->unique()
            ->nullable(false)
            ->index();
            $table->string('phone', 20)
            ->unique()
            ->nullable(true);
            $table->string('email', 255)
            ->unique()
            ->nullable(false)
            ->index();
            $table->string('name', 50)
            ->unique()
            ->nullable(false)
            ->index();
            $table->string('password', 100)
            ->nullable(false);
            $table->foreignId('type_id')
            ->nullable(true)
            ->references('user_type_id')
            ->on('user_type')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->foreignId('status')
            ->nullable(true)
            ->references('id')
            ->on('user_status')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->integer('cabang_id', false, true)
            ->nullable(true);
            $table->integer('store_id', false, true)
            ->nullable(true);
            $table->string('status_ba', 50)
            ->nullable(true);
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('user_info');
        Schema::dropIfExists('user_status');
        Schema::dropIfExists('user_type');
    }
};
