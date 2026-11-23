<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('submissions', 'status_sp2d')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->string('status_sp2d')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('submissions', 'status_sp2d')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->dropColumn('status_sp2d');
            });
        }
    }
};
