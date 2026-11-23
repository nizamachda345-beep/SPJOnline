<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('submissions', 'expires_at')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->timestamp('expires_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('submissions', 'expires_at')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->dropColumn('expires_at');
            });
        }
    }
};
