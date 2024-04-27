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
        Schema::create('master_user', function (Blueprint $table) {
            $table->integer('id', false, true)
            ->default(0)
            ->unique()
            ->nullable(false)
            ->index();
            $table->string('user', 100)
            ->primary()
            ->index();
            $table->string('description', 255)
            ->nullable(true);
            $table->string('password', 255)
            ->nullable(true);
            $table->string('username', 50)
            ->nullable(true)
            ->index();
            $table->string('defaultpassword', 255)
            ->nullable(true);
            $table->string('nik', 50)
            ->nullable(true)
            ->index();
            $table->integer('departmentId', false, true)
            ->nullable(true);
            $table->integer('unitId', false, true)
            ->nullable(true);
            $table->timestamp('entrytime')
            ->default(Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'))
            ->nullable(false);
            $table->string('entryuser', 255)
            ->nullable(true);
            $table->string('entryip', 255)
            ->nullable(true);
            $table->timestamp('updatetime')
            ->default(Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'))
            ->nullable(false);
            $table->string('updateuser', 255)
            ->nullable(true);
            $table->string('updateip', 255)
            ->nullable(true);
            $table->text('avatar')
            ->nullable(true);
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('master_user_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)
            ->primary()
            ->index();
            $table->string('user', 100)
            ->nullable(true)
            ->index();
            $table->string('groupcode', 20)
            ->nullable(true)
            ->index();
            $table->timestamp('entrytime')
            ->default(Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'))
            ->nullable(false);
            $table->string('entryuser', 255)
            ->nullable(true);
            $table->string('entryip', 255)
            ->nullable(true);
            $table->timestamp('updatetime')
            ->default(Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'))
            ->nullable(false);
            $table->string('updateuser', 255)
            ->nullable(true);
            $table->string('updateip', 255)
            ->nullable(true);
            $table->string('created_by', 255)
            ->nullable(true);
            $table->string('updated_by', 255)
            ->nullable(true);
            $table->timestamps();
            $table->softDeletes();            
            $table->foreign('user')
            ->references('user')
            ->on('master_user')
            ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_user_detail');
        Schema::dropIfExists('master_user');
    }
};
