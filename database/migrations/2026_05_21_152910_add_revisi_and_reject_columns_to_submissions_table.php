<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->timestamp('rejected_at')->nullable()->after('updated_at');
            $table->timestamp('revised_at')->nullable()->after('rejected_at');
            $table->timestamp('revisi_at')->nullable()->after('revised_at');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['rejected_at', 'revised_at', 'revisi_at']);
        });
    }
};
