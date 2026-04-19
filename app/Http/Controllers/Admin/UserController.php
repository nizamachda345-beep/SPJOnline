<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles', 'department')->get();
        $roles = Role::all();
        $departments = Department::all();
        return view('admin.users.index', compact('users', 'roles', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
            'department_id' => 'required'
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

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
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

    // Jika password diisi, maka update password
    if ($request->filled('password')) {
        $user->update(['password' => bcrypt($request->password)]);
    }

    // Update Role (Spatie)
    $user->syncRoles([$request->role]);

    return redirect()->back()->with('success', 'Data user berhasil diperbarui.');
}
}