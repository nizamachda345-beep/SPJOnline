@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-users-cog me-2"></i>Manajemen User
            </h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                <i class="fas fa-user-plus me-1"></i> Tambah Akun
            </button>
        </div>
        <div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Lengkap</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Password (Kosongkan jika tidak diganti)</label>
                    <input type="password" name="password" class="form-control" placeholder="******">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Instansi</label>
                    <select name="department_id" id="edit_dept" class="form-select" required>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->nama_dinas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Role / Jabatan</label>
                    <select name="role" id="edit_role" class="form-select" required>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

        <!-- Modal Tambah User -->
        <div class="modal fade" id="modalTambahUser" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('users.store') }}" method="POST" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Tambah Pengguna Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Instansi</label>
                            <select name="department_id" class="form-select" required>
                                <option value="">Pilih Instansi</option>
                                @foreach($departments as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_dinas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Role / Jabatan</label>
                            <select name="role" class="form-select" required>
                                <option value="">Pilih Role</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah User</button>
                    </div>
                </form>
            </div>
        </div>
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Nama Pegawai</th>
                        <th>Email / Username</th>
                        <th>Instansi (Dinas)</th>
                        <th>Role / Jabatan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        
                        <td class="ps-4 fw-bold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge bg-secondary">{{ $user->department->nama_dinas ?? 'Tanpa Dinas' }}</span></td>
                        <td>
                            <span class="badge bg-outline-primary border border-primary text-primary">
                                {{ $user->roles->first()->name ?? '-' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm rounded-pill border bg-white px-1 shadow-sm">
                                {{-- Button Edit dengan Data Attributes untuk Modal --}}
                                <button class="btn btn-link text-info px-2 py-1 btn-edit" 
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-dept="{{ $user->department_id }}"
                                        data-role="{{ $user->roles->first()?->name }}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editUserModal"
                                        title="Edit User">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- Form Delete que usa SweetAlert2 --}}
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" data-confirm-delete="Hapus user <strong>{{ $user->name }}</strong> secara permanen?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger px-2 py-1" title="Hapus User">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit');
        const editForm = document.getElementById('editForm');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');
                const dept = this.getAttribute('data-dept');
                const role = this.getAttribute('data-role');

                // Set Action URL Form secara dinamis
                editForm.action = `/admin/users/${id}`;

                // Isi data ke dalam input modal
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_dept').value = dept;
                document.getElementById('edit_role').value = role;
            });
        });
    });
</script>
@endsection
