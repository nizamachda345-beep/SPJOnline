<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration only supports complete legacy or current schemas.
        // Settings require id, key/setting_key (unique), group, value, type,
        // updated_by and both timestamps. Logs require id, key/setting_key,
        // user/changed_by, old_value, new_value and changed/created_at. The
        // migration does not create or verify foreign keys on pre-existing
        // tables; inspect those separately. It stops before changing either
        // table when a partial or ambiguous schema is found.
        $this->assertSettingsTableIsCompatible();
        $this->assertLogsTableIsCompatible();

        if (Schema::hasTable('system_settings') && Schema::hasColumn('system_settings', 'key')) {
            Schema::table('system_settings', function (Blueprint $table) {
                $table->renameColumn('key', 'setting_key');
                $table->renameColumn('group', 'group_name');
                $table->renameColumn('type', 'value_type');
            });
        }

        if (Schema::hasTable('system_setting_logs') && Schema::hasColumn('system_setting_logs', 'key')) {
            Schema::table('system_setting_logs', function (Blueprint $table) {
                $table->renameColumn('key', 'setting_key');
                $table->renameColumn('changed_by', 'user_id');
                $table->renameColumn('created_at', 'changed_at');
            });
        }

        if (Schema::hasTable('system_setting_logs')) {
            $hasExpectedIndex = collect(Schema::getIndexes('system_setting_logs'))->contains(
                fn (array $index): bool => $index['columns'] === ['setting_key', 'changed_at']
            );

            if (!$hasExpectedIndex) {
                Schema::table('system_setting_logs', function (Blueprint $table) {
                    $table->index(['setting_key', 'changed_at']);
                });
            }
        }
    }

    public function down(): void
    {
        // This is a conditional legacy-shape reversal, not a guaranteed restore
        // of the original schema. The marker columns are not ownership proof;
        // inspect the target schema before rolling this migration back.
        $legacyLogsWereAligned = Schema::hasTable('system_setting_logs')
            && (Schema::hasColumn('system_setting_logs', 'setting_id')
                || Schema::hasColumn('system_setting_logs', 'ip_address')
                || Schema::hasColumn('system_setting_logs', 'user_agent'));

        if ($legacyLogsWereAligned) {
            $indexName = collect(Schema::getIndexes('system_setting_logs'))
                ->first(fn (array $index): bool => $index['columns'] === ['setting_key', 'changed_at'])['name'] ?? null;

            if ($indexName !== null) {
                Schema::table('system_setting_logs', function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
            }

            if (Schema::hasColumn('system_setting_logs', 'setting_key') && !Schema::hasColumn('system_setting_logs', 'key')) {
                Schema::table('system_setting_logs', function (Blueprint $table) {
                    $table->renameColumn('setting_key', 'key');
                    $table->renameColumn('user_id', 'changed_by');
                    $table->renameColumn('changed_at', 'created_at');
                });
            }
        }

        $legacySettingsWereAligned = Schema::hasTable('system_settings')
            && (Schema::hasColumn('system_settings', 'description')
                || Schema::hasColumn('system_settings', 'is_active')
                || Schema::hasColumn('system_settings', 'sort_order'));

        if ($legacySettingsWereAligned && Schema::hasColumn('system_settings', 'setting_key') && !Schema::hasColumn('system_settings', 'key')) {
            Schema::table('system_settings', function (Blueprint $table) {
                $table->renameColumn('setting_key', 'key');
                $table->renameColumn('group_name', 'group');
                $table->renameColumn('value_type', 'type');
            });
        }
    }

    private function assertSettingsTableIsCompatible(): void
    {
        if (!Schema::hasTable('system_settings')) {
            throw new RuntimeException('Cannot align system_settings: table is missing.');
        }

        $hasLegacyKey = Schema::hasColumn('system_settings', 'key');
        $hasCurrentKey = Schema::hasColumn('system_settings', 'setting_key');
        if ($hasLegacyKey === $hasCurrentKey) {
            throw new RuntimeException('Cannot align system_settings: expected exactly one of key or setting_key.');
        }

        $keyColumn = $hasLegacyKey ? 'key' : 'setting_key';
        $groupColumn = $hasLegacyKey ? 'group' : 'group_name';
        $typeColumn = $hasLegacyKey ? 'type' : 'value_type';
        foreach (['id', $keyColumn, $groupColumn, 'value', $typeColumn, 'updated_by', 'created_at', 'updated_at'] as $column) {
            if (!Schema::hasColumn('system_settings', $column)) {
                throw new RuntimeException("Cannot align system_settings: required column {$column} is missing.");
            }
        }

        $hasUniqueKey = collect(Schema::getIndexes('system_settings'))->contains(
            fn (array $index): bool => ($index['unique'] ?? false) && $index['columns'] === [$keyColumn]
        );
        if (!$hasUniqueKey) {
            throw new RuntimeException("Cannot align system_settings: {$keyColumn} must have a unique index.");
        }
    }

    private function assertLogsTableIsCompatible(): void
    {
        if (!Schema::hasTable('system_setting_logs')) {
            throw new RuntimeException('Cannot align system_setting_logs: table is missing.');
        }

        $hasLegacyKey = Schema::hasColumn('system_setting_logs', 'key');
        $hasCurrentKey = Schema::hasColumn('system_setting_logs', 'setting_key');
        if ($hasLegacyKey === $hasCurrentKey) {
            throw new RuntimeException('Cannot align system_setting_logs: expected exactly one of key or setting_key.');
        }

        $columns = $hasLegacyKey
            ? ['id', 'key', 'changed_by', 'old_value', 'new_value', 'created_at']
            : ['id', 'setting_key', 'user_id', 'old_value', 'new_value', 'changed_at'];
        foreach ($columns as $column) {
            if (!Schema::hasColumn('system_setting_logs', $column)) {
                throw new RuntimeException("Cannot align system_setting_logs: required column {$column} is missing.");
            }
        }
    }
};
