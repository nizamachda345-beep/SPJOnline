namespace App\Services;

use App\Models\Submission;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class SubmissionService
{
    public function storeSubmission($data, $files, $userId)
    {
        // 1. Simpan Data Form
        $submission = Submission::create([
            'user_id' => $userId,
            'department_id' => $data['department_id'],
            'nomor_spp_spm' => $data['nomor_spp_spm'],
            'total_pencairan' => $data['total_pencairan'],
            // ... field lainnya
        ]);

        // 2. Simpan Multiple Files
        foreach ($files as $jenis => $file) {
            $path = $file->store('documents/' . date('Y/m'));
            Document::create([
                'submission_id' => $submission->id,
                'jenis_dokumen' => $jenis,
                'file_path' => $path,
                'status_dokumen' => 'pending'
            ]);
        }

        return $submission;
    }
}