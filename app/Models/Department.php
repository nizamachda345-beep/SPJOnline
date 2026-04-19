<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Opsional: untuk type-hinting yang lebih pro

class Department extends Model
{
    // 1. Definisikan kolom yang boleh diisi (Mass Assignment)
    protected $fillable = ['nama_dinas', 'kode_dinas'];

    /**
     * Relasi ke Model User
     * Satu Dinas memiliki banyak User (Operator)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
} // <--- Pastikan kurung kurawal penutup class ada di paling bawah