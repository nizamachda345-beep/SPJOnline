<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('error_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('actor_name');
            $table->string('role_name')->nullable();
            $table->string('department_name')->nullable();
            $table->string('error_type')->nullable();
            $table->text('error_message');
            $table->string('page_path', 2048)->nullable();
            $table->string('http_method', 10)->nullable();
            $table->string('browser_info', 1000)->nullable();
            $table->string('status', 20)->default('open')->index();
            $table->timestamp('occurred_at')->useCurrent()->index();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('error_reports');
    }
};
