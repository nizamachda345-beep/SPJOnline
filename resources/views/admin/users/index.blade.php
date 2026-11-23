{{-- View: admin/users/index --}}
@extends('layouts.app')

@section('content')
@php
    $displayRoleName = static fn ($roleName) => match ($roleName) {
        'super_admin' => 'MASTER',
        'operator' => 'Operator',
        'kabid_perbendaharaan' => 'Validator',
        'kasie_bud' => 'Verifikator',
        'bpk' => 'BPK',
        default => $roleName ?? '-',
    };
@endphp
<style>
    .user-filter-submit {
        color: #e2e8f0;
        background: rgba(15, 23, 42, .82);
        border: 1px solid rgba(148, 163, 184, .24);
        transition: background-color .16s ease, border-color .16s ease;
    }
    .user-filter-submit:hover,
    .user-filter-submit:focus-visible {
        color: #fff;
        background: rgba(30, 41, 59, .96);
        border-color: rgba(148, 163, 184, .42);
    }
    .user-role-badge {
        background-color: rgba(37, 99, 235, .12) !important;
        border-color: rgba(59, 130, 246, .5) !important;
        color: #60a5fa !important;
    }
    .user-role-badge-master {
        background-color: rgba(127, 29, 29, .16) !important;
        border-color: rgba(185, 28, 28, .55) !important;
        color: #f87171 !important;
    }
    .user-role-badge-bpk {
        background-color: rgba(202, 138, 4, .15) !important;
        border-color: rgba(234, 179, 8, .55) !important;
        color: #facc15 !important;
    }
    .user-role-badge-verifikator {
        background-color: rgba(22, 101, 52, .18) !important;
        border-color: rgba(34, 197, 94, .5) !important;
        color: #4ade80 !important;
    }
</style>
<div class="container-fluid px-4 text-white"min-height: 100vh;">
    <div class="d-flex justify-content-between align-items-center glass-header-dark shadow-sm  rounded-4 p-4 mb-3">
        <div class="d-flex align-items-center">
            <div class="me-3 text-light">
                <i class="fas fa-users fa-3x text-primary"></i>
            </div>
            <div>
                <h3 class="fw-bold text-light mb-1">Manajemen User</h3>
                <p class="text-light mb-0">Kelola akun pengguna sistem.</p>
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
            <button class="btn btn-primary btn-sm rounded-pill px-4 py-2 fw-semibold shadow-sm border border-primary border-opacity-20 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                <i class="fas fa-user-plus"></i> Tambah Akun
            </button>
        </div>
    </div>

    <form action="{{ route('users.index') }}" method="GET" class="card glass-card-dark border-0 rounded-4 shadow-sm mb-3">
        <div class="card-body p-3">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-lg-7">
                    <label for="user-search" class="form-label small text-white-50 mb-1">Cari pengguna atau dinas</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-white border-opacity-10 text-white-50"><i class="fas fa-search"></i></span>
                        <input type="search" id="user-search" name="search" value="{{ request('search') }}" class="form-control bg-dark border-white border-opacity-10 text-light" placeholder="Nama user, nama dinas, atau kode dinas...">
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="user-role-filter" class="form-label small text-white-50 mb-1">Role / Jabatan</label>
                    <select id="user-role-filter" name="role" class="form-select bg-dark border-white border-opacity-10 text-light custom-dark-select">
                        <option value="">Semua Role</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" @selected(request('role') === $r->name)>{{ $displayRoleName($r->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn user-filter-submit flex-grow-1"><i class="fas fa-filter me-1"></i> Filter</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-light" aria-label="Reset filter" title="Reset filter"><i class="fas fa-rotate-left"></i></a>
                </div>
            </div>
        </div>
    </form>

    <div id="users-table-container" class="card glass-card-dark border-0 rounded-4 overflow-hidden shadow-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0 text-white">
                    <thead class="text-white-50 small text-uppercase fw-bold text-nowrap" style="background: rgba(255, 255, 255, 0.02);">
                        <tr>
                            <th class="ps-4 py-3 border-bottom border-white border-opacity-10 text-start">User</th>
                            <th class="py-3 border-bottom border-white border-opacity-10">Email / Username</th>
                            <th class="py-3 border-bottom border-white border-opacity-10">Instansi (Dinas)</th>
                            <th class="py-3 border-bottom border-white border-opacity-10">Role / Jabatan</th>
                            <th class="py-3 text-center pe-4 border-bottom border-white border-opacity-10" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="border-0 text-nowrap">
                        @forelse($users as $user)
                        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.04); transition: all 0.2s;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseout="this.style.backgroundColor='transparent'">

                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle border border-white border-opacity-10 bg-white bg-opacity-5" style="width: 30px; height: 30px; min-width: 30px;">
                                        <i class="fas fa-user" style="font-size: 0.85rem;"></i>
                                    </div>
                                    <span class="fw-bold text-white text-truncate" style="max-width: 180px;" title="{{ $user->name }}">{{ $user->name }}</span>
                                </div>
                            </td>

                            <td class="text-white-50">{{ $user->email }}</td>

                            <td>
                                <span class="rounded-3 py-1.5 fw-medium d-inline-block text-truncate" style="max-width: 220px;" title="{{ $user->department->nama_dinas ?? 'Tanpa Dinas' }}">
                                    <i class="fas fa-building me-1 opacity-50"></i>{{ $user->department->nama_dinas ?? 'Tanpa Dinas' }}
                                </span>
                            </td>

                            <td>
                                @php
                                    $userRoleName = $user->roles->first()?->name;
                                    $userRoleBadgeClass = match ($userRoleName) {
                                        'super_admin' => 'user-role-badge-master',
                                        'bpk' => 'user-role-badge-bpk',
                                        'kasie_bud', 'kabid_perbendaharaan' => 'user-role-badge-verifikator',
                                        default => '',
                                    };
                                @endphp
                                <span class="badge rounded-pill px-2 py-1.5 text-uppercase tracking-wider fw-bold border small user-role-badge {{ $userRoleBadgeClass }}" style="font-size: 0.7rem;">
                                    <i class="fas fa-shield-alt me-1 opacity-75"></i>{{ $displayRoleName($userRoleName) }}
                                </span>
                            </td>

                            <td class="text-center pe-4">
                                <div class="btn-group btn-group-sm rounded-pill border border-white border-opacity-10 bg-white bg-opacity-5 p-1 shadow-sm" style="backdrop-filter: blur(4px);">

                                    <button class="btn btn-link text-info px-2 py-1 btn-edit border-0 shadow-none"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-dept="{{ $user->department_id }}"
                                            data-role="{{ $user->roles->first()?->name }}"
                                            data-update-url="{{ route('users.update', $user->id) }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUserModal"
                                            title="Edit User">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" data-confirm-delete="Hapus user <strong>{{ $user->name }}</strong> secara permanen?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger px-2 py-1 border-0 shadow-none" title="Hapus User">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-white-50 py-5">Tidak ada pengguna yang cocok dengan filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
            <div class="my-3 d-flex justify-content-center">
                <div class="glass-pagination bg-opacity-75 px-4 py-1 rounded-pill shadow-sm ">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal: Tambah pengguna --}}
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg text-light rounded-4" style="background: #111827; border: 1px solid rgba(255,255,255,0.08) !important;">
            <div class="modal-header border-bottom border-white border-opacity-10 py-3 px-4">
                <h6 class="modal-title fw-bold m-0 text-light d-flex align-items-center gap-2">
                    <i class="fas fa-user-plus text-primary"></i> Tambah Pengguna Baru
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_form_context" value="create">
                <div class="modal-body p-4" style="background: rgba(255,255,255,0.01);">
                    <div class="mb-3">
                        <label class="form-label extra-small text-light fw-bold tracking-wide text-uppercase mb-1" style="opacity: 0.85;">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control bg-dark border-white border-opacity-10 text-light rounded-3 py-2 @error('name') is-invalid @enderror" placeholder="Masukkan nama pegawai" style="outline:none;" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label extra-small text-light fw-bold tracking-wide text-uppercase mb-1" style="opacity: 0.85;">Email / Username</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control bg-dark border-white border-opacity-10 text-light rounded-3 py-2 @error('email') is-invalid @enderror" placeholder="contoh@domain.com" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label extra-small text-light fw-bold tracking-wide text-uppercase mb-1" style="opacity: 0.85;">Password</label>
                            <input type="password" name="password" class="form-control bg-dark border-white border-opacity-10 text-light rounded-3 py-2 @error('password') is-invalid @enderror" placeholder="******" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label extra-small text-light fw-bold tracking-wide text-uppercase mb-1" style="opacity: 0.85;">Konfirmasi</label>
                            <input type="password" name="password_confirmation" class="form-control bg-dark border-white border-opacity-10 text-light rounded-3 py-2 @error('password') is-invalid @enderror" placeholder="******" required>
                        </div>
                        @error('password') <div class="col-12 invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label extra-small text-light fw-bold tracking-wide text-uppercase mb-1" style="opacity: 0.85;">Instansi Terkait</label>
                        <select name="department_id" class="form-select bg-dark border-white border-opacity-10 text-light rounded-3 py-2 custom-dark-select @error('department_id') is-invalid @enderror" required>
                            <option value="" class="text-white-50">Pilih Instansi / OPD</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}" @selected((string) old('department_id') === (string) $d->id)>{{ $d->nama_dinas }}</option>
                            @endforeach
                        </select>
                        @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label extra-small text-light fw-bold tracking-wide text-uppercase mb-1" style="opacity: 0.85;">Hak Akses Sistem</label>
                        <select name="role" id="create_role" class="form-select bg-dark border-white border-opacity-10 text-light rounded-3 py-2 custom-dark-select @error('role') is-invalid @enderror" required>
                            <option value="" class="text-white-50">Pilih Tingkatan Role</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}" @selected(old('role') === $r->name)>{{ $displayRoleName($r->name) }}</option>
                            @endforeach
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-0 d-none" id="masterCodeField">
                        <label for="master_code" class="form-label extra-small text-light fw-bold tracking-wide text-uppercase mb-1" style="opacity: 0.85;">Kode Otorisasi MASTER</label>
                        <input type="password" name="master_code" id="master_code" class="form-control bg-dark border-white border-opacity-10 text-light rounded-3 py-2 @error('master_code') is-invalid @enderror" autocomplete="off" placeholder="Masukkan kode otorisasi" aria-describedby="master-code-error">
                        @error('master_code')
                            <div class="invalid-feedback" id="master-code-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 bg-dark bg-opacity-40 py-3 px-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3 bg-opacity-20 border-0 text-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm text-light">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Ubah data pengguna --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg text-light rounded-4" style="background: #111827; border: 1px solid rgba(255,255,255,0.08) !important;">
            <div class="modal-header border-bottom border-white border-opacity-10 py-3 px-4">
                <h6 class="modal-title fw-bold m-0 text-light d-flex align-items-center gap-2">
                    <i class="fas fa-user-edit text-info"></i> Ubah Data Pengguna
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" action="{{ route('users.update', 0) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4" style="background: rgba(255,255,255,0.01);">
                    <div class="mb-3">
                        <label class="form-label extra-small text-light fw-bold tracking-wide text-uppercase mb-1" style="opacity: 0.85;">Nama Lengkap</label>
                        <input type="text" name="name" id="edit_name" class="form-control bg-dark border-white border-opacity-10 text-light rounded-3 py-2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label extra-small text-light fw-bold tracking-wide text-uppercase mb-1" style="opacity: 0.85;">Email / Username</label>
                        <input type="email" name="email" id="edit_email" class="form-control bg-dark border-white border-opacity-10 text-light rounded-3 py-2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label extra-small text-light fw-bold tracking-wide text-uppercase mb-1" style="opacity: 0.85;">Ganti Password <span class="text-info lowercase">(opsional)</span></label>
                        <input type="password" name="password" class="form-control bg-dark border-white border-opacity-10 text-light rounded-3 py-2" placeholder="Kosongkan jika sandi tetap">
                    </div>
                    <div class="mb-3">
                        <label class="form-label extra-small text-light fw-bold tracking-wide text-uppercase mb-1" style="opacity: 0.85;">Instansi Terkait</label>
                        <select name="department_id" id="edit_dept" class="form-select bg-dark border-white border-opacity-10 text-light rounded-3 py-2 custom-dark-select" required>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->nama_dinas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label extra-small text-light fw-bold tracking-wide text-uppercase mb-1" style="opacity: 0.85;">Hak Akses Sistem</label>
                        <select name="role" id="edit_role" class="form-select bg-dark border-white border-opacity-10 text-light rounded-3 py-2 custom-dark-select" required>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $displayRoleName($r->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 bg-dark bg-opacity-40 py-3 px-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3 bg-opacity-20 border-0 text-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info btn-sm rounded-pill px-4 text-white shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>





<script>
    document.addEventListener('DOMContentLoaded', function() {
        const createRole = document.getElementById('create_role');
        const masterCodeField = document.getElementById('masterCodeField');
        const masterCodeInput = document.getElementById('master_code');

        const updateMasterCodeField = () => {
            const requiresCode = createRole.value === 'super_admin';
            masterCodeField.classList.toggle('d-none', !requiresCode);
            masterCodeInput.required = requiresCode;
            if (!requiresCode) masterCodeInput.value = '';
        };

        createRole.addEventListener('change', updateMasterCodeField);
        updateMasterCodeField();

        @if(old('_form_context') === 'create')
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalTambahUser')).show();
        @endif
    });

    document.addEventListener('DOMContentLoaded', function() {
        const editForm = document.getElementById('editForm');
        const editModal = document.getElementById('editUserModal');

        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            editForm.action = button.getAttribute('data-update-url');

            document.getElementById('edit_name').value = button.getAttribute('data-name');
            document.getElementById('edit_email').value = button.getAttribute('data-email');
            document.getElementById('edit_dept').value = button.getAttribute('data-dept');
            document.getElementById('edit_role').value = button.getAttribute('data-role');
        });
    });
</script>


<style>
    .custom-dark-select option {
        background-color: #111827 !important; /* Menyamakan warna dropdown dengan modal */
        color: #f8fafc !important; /* Memaksa teks opsi dropdown tetap putih */
    }
    /* Memperbaiki warna teks placeholder ketikan agar abu redup sebelum diketik */
    .form-control::placeholder {
        color: rgba(248, 250, 252, 0.4) !important;
    }
</style>



<style>
    .extra-small { font-size: 0.68rem; }
    .form-control:focus, .form-select:focus {
        background-color: #0f172a !important;
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important;
        color: #fff !important;
    }
</style>

@endsection
