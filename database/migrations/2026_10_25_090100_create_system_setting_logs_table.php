<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('system_setting_logs')) {
            Schema::create('system_setting_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('setting_key');
                $table->longText('old_value')->nullable();
                $table->longText('new_value')->nullable();
                $table->timestamp('changed_at')->useCurrent();
                $table->index(['setting_key', 'changed_at']);
            });
        }
    }

    public function down(): void
    {
        // up() may have left a pre-existing table untouched. There is no
        // ownership marker to distinguish that table from one created here,
        // so rollback must not drop potentially user-owned audit history.
    }
};
