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
            $table->unsignedBigInteger('id', true)
            ->primary()
            ->index();
            $table->string('name', 255)
            ->nullable(false)
            ->index();
            $table->timestamp('modtime')
            ->nullable(false)
            ->default(Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'));
        });

        Schema::create('user_info', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary()
            ->index();
            $table->string('number', 10)
            ->nullable(false)
            ->index();
            $table->string('nik', 20)
            ->unique()
            ->nullable(true)
            ->index();
            $table->string('fullname', 200)
            ->nullable(false)
            ->index();
            $table->string('phone', 20)
            ->unique()
            ->nullable(false)
            ->index();
            $table->string('email', 255)
            ->unique()
            ->nullable(false)
            ->index();
            $table->string('username', 50)
            ->unique()
            ->nullable(false)
            ->index();
            $table->string('password', 100)
            ->nullable(false);
            $table->integer('type_id', false, true)
            ->default(0)
            ->index()
            ->nullable(false);
            $table->integer('status', false, true)
            ->default(1)
            ->nullable(false)
            ->index();
            $table->integer('cabang_id', false, true)
            ->nullable(true);
            $table->integer('store_id', false, true)
            ->default(0)
            ->nullable(false)
            ->index();
            $table->string('status_ba', 50)
            ->nullable(true);
            $table->timestamp('modtime', 0)
            ->default(Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'))
            ->nullable(false);
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
        Schema::dropIfExists('user_type');
        Schema::dropIfExists('user_info');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
