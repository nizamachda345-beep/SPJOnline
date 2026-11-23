<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->enum('status_audit', ['belum_diaudit', 'sesuai', 'dengan_catatan'])
                  ->default('belum_diaudit')
                  ->after('status'); // Meletakkan kolom setelah kolom status utama

            $table->text('catatan_audit')
                  ->nullable()
                  ->after('status_audit');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['status_audit', 'catatan_audit']);
        });
    }
};
