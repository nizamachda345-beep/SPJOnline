<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaRekon extends Model
{
    protected $table = 'ba_rekons';

    protected $fillable = [
        'department_id',
        'user_id',
        'bulan',
        'tahun',
        'file_path',
        'status',
        'catatan',
        'uploaded_at'
    ];

    // Method: department
    public function department() {
        return $this->belongsTo(Department::class);
    }

    // Method: user
    public function user() {
        return $this->belongsTo(User::class);
    }
}
