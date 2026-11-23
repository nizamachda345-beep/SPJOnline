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
        Schema::table('submissions', function (Blueprint $table) {
            $table->foreignId('tolak_by')
                  ->nullable()
                  ->before('revisi_at') // Menempatkannya setelah kolom status (opsional, sesuaikan kolom Anda)
                  ->constrained('users')
                  ->onDelete('set null'); // Jika user dihapus, kolom ini diset jadi null agar data submission tidak hilang
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['tolak_by']);
            $table->dropColumn('tolak_by');
        });
    }
};
