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
            ->primary()
            ->index();
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
            ->primary()
            ->index();
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
            ->primary()
            ->index();
            $table->string('user_number', 10)
            ->nullable(false)
            ->index();
            $table->string('user_nik', 20)
            ->unique()
            ->nullable(true)
            ->index();
            $table->string('user_fullname', 200)
            ->nullable(false)
            ->index();
            $table->string('user_phone', 20)
            ->unique()
            ->nullable(false)
            ->index();
            $table->string('user_email', 255)
            ->unique()
            ->nullable(false)
            ->index();
            $table->string('user_name', 50)
            ->unique()
            ->nullable(false)
            ->index();
            $table->string('user_password', 100)
            ->nullable(false);
            $table->foreignId('user_type_id')
            ->index()
            ->nullable(false)
            ->references('user_type_id')
            ->on('user_type')
            ->cascadeOnDelete();
            $table->foreignId('user_status')
            ->index()
            ->nullable(false)
            ->references('id')
            ->on('user_status')
            ->cascadeOnDelete();
            $table->integer('cabang_id', false, true)
            ->nullable(true);
            $table->integer('store_id', false, true)
            ->default(0)
            ->nullable(false)
            ->index();
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
