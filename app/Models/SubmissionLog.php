<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionLog extends Model
{
    protected $fillable = ['submission_id', 'user_id', 'status_to', 'message'];

    /**
     * Dapatkan data pengajuan yang memiliki log ini.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id', 'id');
    }

    /**
     * Jika Anda juga ingin menghubungkan log ke User yang melakukan aksi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}