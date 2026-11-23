<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccessModeController extends Controller
{
    public const MODES = [
        'super_admin' => 'Master',
        'operator' => 'Operator',
        'kasie_bud' => 'Verifikator',
        'kabid_perbendaharaan' => 'Validator',
        'bpk' => 'BPK',
    ];

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isActualSuperAdmin(), 403);

        $validated = $request->validate([
            'access_mode' => ['required', 'in:' . implode(',', array_keys(self::MODES))],
        ]);

        $request->session()->put('master_access_mode', $validated['access_mode']);

        return redirect()->route($validated['access_mode'] === 'super_admin'
            ? 'admin.dashboard'
            : ($validated['access_mode'] === 'bpk' ? 'bpk.dashboard' : 'dashboard'));
    }
}
