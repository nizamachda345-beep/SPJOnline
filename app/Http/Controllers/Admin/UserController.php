<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    // Method: index
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $selectedRole = (string) $request->query('role', '');

        $usersQuery = User::with('roles', 'department')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhereHas('department', function ($departmentQuery) use ($search) {
                            $departmentQuery->where('nama_dinas', 'like', "%{$search}%")
                                ->orWhere('kode_dinas', 'like', "%{$search}%");
                        });
                });
            })
            ->when($selectedRole !== '', function ($query) use ($selectedRole) {
                $query->whereHas('roles', fn ($roleQuery) => $roleQuery->where('name', $selectedRole));
            });

        $users = $usersQuery->latest()->paginate(10)->withQueryString();

        $roles = Role::all();
        $departments = Department::all();

        return view('admin.users.index', compact('users', 'roles', 'departments'));
    }

    // Method: store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required',
            'department_id' => 'required',
            'master_code' => [
                'nullable',
                'required_if:role,super_admin',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('role') === 'super_admin'
                        && filled($value)
                        && !hash_equals('nzmachda', (string) $value)) {
                        $fail('Kode otorisasi MASTER tidak valid. Silakan hubungi developer.');
                    }
                },
            ],
        ], [
            'master_code.required_if' => 'Kode otorisasi MASTER wajib diisi. Silakan hubungi developer.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'department_id' => $request->department_id
        ]);

        $user->assignRole($request->role);
        return redirect()->back()->with('success', 'User berhasil didaftarkan.');
    }

    // Method: destroy
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    // Method: update
    public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email,' . $id,
        'department_id' => 'required',
        'role' => 'required'
    ]);

    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'department_id' => $request->department_id,
    ]);

    if ($request->filled('password')) {
        $user->update(['password' => bcrypt($request->password)]);
    }

    $user->syncRoles([$request->role]);

    return redirect()->back()->with('success', 'Data user berhasil diperbarui.');
}
}
