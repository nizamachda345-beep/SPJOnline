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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); 
            $table->foreignId('department_id')->constrained(); 
            $table->string('nomor_spp_spm');
            $table->date('tanggal');
            $table->string('judul_kegiatan');
            $table->string('judul_sub_kegiatan');
            $table->text('perihal');
            $table->decimal('total_pencairan', 15, 2);
            $table->string('status')->default('pending'); 

            // --- KOLOM JEJAK DIGITAL ---
            $table->string('verified_by_name')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('validated_by_name')->nullable();
            $table->timestamp('validated_at')->nullable();
            // ---------------------------

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};