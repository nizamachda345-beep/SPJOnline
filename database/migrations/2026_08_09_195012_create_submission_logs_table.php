<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('submission_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('submission_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // Operator yang menghapus
            $table->string('action')->default('DELETED');
            $table->json('submission_data'); // Menyimpan semua metadata pengajuan
            $table->json('documents_data')->nullable(); // Menyimpan info berkas yang ikut terhapus
            $table->timestamp('deleted_at')->useCurrent(); // Waktu data asli dihapus
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_logs');
    }
};
