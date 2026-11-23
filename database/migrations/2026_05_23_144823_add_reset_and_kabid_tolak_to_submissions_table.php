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
        Schema::table('submissions', function (Blueprint $blueprint) {
            $blueprint->timestamp('reset_at')->nullable()->after('revisi_at');
            $blueprint->timestamp('kabid_tolak_at')->nullable()->after('reset_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['reset_at', 'kabid_tolak_at']);
        });
    }
};
