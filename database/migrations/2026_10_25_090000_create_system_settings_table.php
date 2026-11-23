<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table) {
                $table->id();
                $table->string('setting_key')->unique();
                $table->string('group_name', 50);
                $table->longText('value')->nullable();
                $table->string('value_type', 20)->default('text');
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // up() may have left a pre-existing table untouched. There is no
        // ownership marker to distinguish that table from one created here,
        // so rollback must not drop potentially user-owned settings/data.
    }
};
