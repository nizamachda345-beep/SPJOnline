<?php
// Routes: console
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Notifications\SystemActivityNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

// Function: Menampilkan kutipan inspirasi
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Function: Memproses pengajuan kedaluwarsa dan mengirim notifikasi
Schedule::call(function () {
    $targetRoles = ['kabid_perbendaharaan', 'kasie_bud', 'super_admin'];
    $managementUsers = User::whereHas('roles', function($q) use ($targetRoles) {
        $q->whereIn('name', $targetRoles);
    })->get();

    Submission::where('status', 'pending')
        ->where('expires_at', '<', Carbon::now())
        ->chunkById(100, function ($expiredSubmissions) use ($managementUsers) {
            foreach ($expiredSubmissions as $submission) {
                try {
                    DB::transaction(function () use ($submission, $managementUsers) {
                        Log::info('Proses Expired: Submission ID ' . $submission->id);

                        $submission->update(['status' => 'expired']);

                        $recipients = collect();

                        $operator = User::find($submission->user_id);
                        if ($operator) {
                            $recipients->push($operator);
                        }

                        $recipients = $recipients->merge($managementUsers);

                        $uniqueRecipients = $recipients->unique('id');

                        $additionalData = [
                            'submission_id'   => $submission->id,
                            'nomor_spm'       => $submission->nomor_spm,
                            'nomor_spp'       => $submission->nomor_spp,
                            'total_pencairan' => $submission->total_pencairan,
                            'status'          => 'expired',
                        ];

                        foreach ($uniqueRecipients as $recipient) {
                            $recipient->notify(new SystemActivityNotification(
                                'Pengajuan Kedaluwarsa',
                                'SPM No. ' . $submission->nomor_spm . ' telah kedaluwarsa karena melewati batas waktu pengajuan.',
                                'fa-clock',
                                'danger',
                                $additionalData
                            ));
                        }

                        Log::info('Notifikasi expired berhasil dikirim untuk Submission ID: ' . $submission->id);
                    });
                } catch (\Exception $e) {
                    Log::error('Gagal memproses expired Submission ID ' . $submission->id . ': ' . $e->getMessage());
                }
            }
        });
})->daily();

// Function: Membersihkan log pengajuan lama
Schedule::call(function () {
    try {
        DB::table('submission_logs')
            ->where('deleted_at', '<', now()->subYear())
            ->delete();
        Log::info('Berhasil membersihkan submission_logs yang kedaluwarsa lebih dari 1 tahun.');
    } catch (\Exception $e) {
        Log::error('Gagal membersihkan submission_logs: ' . $e->getMessage());
    }
})->daily();
