<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->date('tanggal_spp')->nullable()->after('nomor_spp');
            $table->date('tanggal_spm')->nullable()->after('nomor_spm');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['tanggal_spp', 'tanggal_spm']);
        });
    }
};
