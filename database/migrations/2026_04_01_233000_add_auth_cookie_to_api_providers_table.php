<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('api_providers', function (Blueprint $table): void {
            if (! Schema::hasColumn('api_providers', 'auth_cookie')) {
                $table->text('auth_cookie')->nullable()->after('api_key');
            }
        });
    }

    public function down(): void
    {
        Schema::table('api_providers', function (Blueprint $table): void {
            if (Schema::hasColumn('api_providers', 'auth_cookie')) {
                $table->dropColumn('auth_cookie');
            }
        });
    }
};
