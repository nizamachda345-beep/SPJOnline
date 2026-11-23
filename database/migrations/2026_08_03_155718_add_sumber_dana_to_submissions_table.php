<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('submissions', 'sumber_dana')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->string('sumber_dana')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('submissions', 'sumber_dana')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->dropColumn('sumber_dana');
            });
        }
    }
};
