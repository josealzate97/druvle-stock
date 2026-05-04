<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE sessions MODIFY user_id VARCHAR(255) NULL');

        Schema::table('sessions', function (Blueprint $table) {
            $table->string('tenant_id')->nullable()->after('user_id');
            $table->timestamp('login_at')->nullable()->after('tenant_id');
            $table->timestamp('logout_at')->nullable()->after('login_at');
        });

        Schema::create('user_session_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('session_id')->index();
            $table->string('user_id')->index();
            $table->string('tenant_id')->nullable()->index();
            $table->string('username', 50);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('login_at');
            $table->timestamp('logout_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_session_logs');

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn(['tenant_id', 'login_at', 'logout_at']);
        });

        DB::statement('ALTER TABLE sessions MODIFY user_id BIGINT UNSIGNED NULL');
    }
};