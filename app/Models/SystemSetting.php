<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'setting_key',
        'group_name',
        'value',
        'value_type',
        'updated_by',
    ];
}
