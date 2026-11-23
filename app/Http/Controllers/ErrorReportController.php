<?php

namespace App\Http\Controllers;

use App\Models\ErrorReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ErrorReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'error_type' => ['nullable', 'string', 'max:255'],
            'error_message' => ['required', 'string', 'max:5000'],
            'page_path' => ['nullable', 'string', 'max:2048'],
            'http_method' => ['nullable', 'string', 'max:10'],
            'browser_info' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $role = $user->roles->pluck('name')->map(fn (string $name) => Str::headline($name))->implode(', ');

        $report = ErrorReport::create([
            'user_id' => $user->getKey(),
            'actor_name' => $user->name,
            'role_name' => $role ?: null,
            'department_name' => $user->department?->nama_dinas,
            'error_type' => $this->redactSensitiveText($validated['error_type'] ?? ''),
            'error_message' => $this->redactSensitiveText($validated['error_message']),
            'page_path' => $this->safePath($validated['page_path'] ?? null),
            'http_method' => strtoupper($validated['http_method'] ?? $request->method()),
            'browser_info' => $this->redactSensitiveText($validated['browser_info'] ?? ''),
            'occurred_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Log kendala berhasil dikirim ke Super Admin.',
            'report_id' => $report->getKey(),
        ], 201);
    }

    private function safePath(?string $path): ?string
    {
        if (!filled($path)) {
            return null;
        }

        $pathOnly = parse_url($path, PHP_URL_PATH);

        return is_string($pathOnly) ? Str::limit($pathOnly, 2048, '') : null;
    }

    private function redactSensitiveText(string $value): ?string
    {
        $value = mb_substr($value, 0, 5000);
        $value = preg_replace('/(?i)(password|token|authorization|cookie|secret)(\s*[=:]\s*)[^\s,;]+/', '$1$2[REDACTED]', $value) ?? $value;

        return filled($value) ? $value : null;
    }
}
