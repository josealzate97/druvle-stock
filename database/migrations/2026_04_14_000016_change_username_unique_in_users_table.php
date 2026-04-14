<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar unique global en username
            $table->dropUnique(['username']);
            // Nuevo unique compuesto: un mismo username puede existir en diferentes tenants
            $table->unique(['username', 'tenant_id'], 'users_username_tenant_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_username_tenant_unique');
            $table->unique('username');
        });
    }
};
