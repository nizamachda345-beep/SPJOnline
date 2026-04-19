@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    {{-- Header Section: Sangat Rapat ke Atas --}}
    <div class="row align-items-center pt-2 mb-3">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white shadow-sm px-3 py-1 rounded-pill mb-1" style="width: fit-content;">
                    <li class="breadcrumb-item small"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item small active text-primary fw-bold" aria-current="page">Manajemen Dinas</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark m-0">Master Data Dinas</h4>
        </div>
        <div class="col-md-6 text-md-end">
            <button class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahDinas">
                <i class="fas fa-plus-circle me-1"></i> Tambah Dinas
            </button>
        </div>
    </div>

    <div class="row">
        {{-- Kolom Kiri: Tabel Daftar Dinas --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-list me-2 text-primary"></i>Daftar Instansi / OPD</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase fw-bold">
                                <tr>
                                    <th class="ps-4 py-3">Nama Dinas</th>
                                    <th class="py-3">Kode</th>
                                    <th class="py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departments as $dept)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                                                <i class="fas fa-building"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $dept->nama_dinas }}</div>
                                                <div class="extra-small text-muted">{{ $dept->users_count }} Operator Terdaftar</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1 fw-normal">{{ $dept->kode_dinas ?? '-' }}</span></td>
                                    <td class="text-center">
                                        <td class="text-center pe-3">
                                            <div class="btn-group btn-group-sm rounded-pill border bg-white px-1 shadow-sm">
                                                {{-- Button Edit --}}
                                                <button class="btn btn-link text-info px-2 py-1 btn-edit-dept" 
                                                        data-id="{{ $dept->id }}"
                                                        data-nama="{{ $dept->nama_dinas }}"
                                                        data-kode="{{ $dept->kode_dinas }}"
                                                        title="Edit Data">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                {{-- Form Delete --}}
                                                <form action="{{ route('department.destroy', $dept->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus dinas ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger px-2 py-1">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">Belum ada data dinas.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Ringkasan / Info Statistik --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-dark text-white mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 small uppercase text-primary">Statistik Global</h6>
                    <div class="d-flex align-items-center">
                        <h2 class="m-0 fw-bold">{{ $departments->count() }}</h2>
                        <span class="ms-2 badge bg-primary bg-opacity-25 text-primary">Instansi Terdata</span>
                    </div>
                    <hr class="border-secondary opacity-25 my-3">
                    <p class="small text-light-50 m-0">Data ini digunakan untuk membagi hak akses berkas berdasarkan instansi masing-masing operator.</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 border-start border-primary border-4">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-dark mb-2 small">Informasi Sistem</h6>
                    <p class="small text-muted mb-0">Pastikan nama dinas sesuai dengan nomenklatur resmi untuk mempermudah audit BPK.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH DINAS --}}
<div class="modal fade" id="modalTambahDinas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4">
                <h6 class="modal-title fw-bold m-0 text-dark">Tambah Data Dinas Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('department.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="extra-small text-muted fw-bold mb-1 uppercase">Nama Dinas Lengkap</label>
                        <input type="text" name="nama_dinas" class="form-control rounded-3 border-light-subtle py-2" placeholder="Contoh: Dinas Kesehatan" required>
                    </div>
                    <div class="mb-0">
                        <label class="extra-small text-muted fw-bold mb-1 uppercase">Kode Singkatan</label>
                        <input type="text" name="kode_dinas" class="form-control rounded-3 border-light-subtle py-2" placeholder="Contoh: DINKES">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT DINAS --}}
<div class="modal fade" id="modalEditDinas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4">
                <h6 class="modal-title fw-bold m-0 text-dark">Edit Data Dinas</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditDinas" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="extra-small text-muted fw-bold mb-1 uppercase">Nama Dinas Lengkap</label>
                        <input type="text" name="nama_dinas" id="edit_nama_dinas" class="form-control rounded-3 py-2" required>
                    </div>
                    <div class="mb-0">
                        <label class="extra-small text-muted fw-bold mb-1 uppercase">Kode Singkatan</label>
                        <input type="text" name="kode_dinas" id="edit_kode_dinas" class="form-control rounded-3 py-2">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info btn-sm rounded-pill px-4 shadow-sm text-white">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.75rem; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
    .btn-link { text-decoration: none; }
    .breadcrumb-item + .breadcrumb-item::before { content: "•"; color: #dee2e6; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Menangani Klik Tombol Edit
    const editButtons = document.querySelectorAll('.btn-edit-dept');
    const editModal = new bootstrap.Modal(document.getElementById('modalEditDinas'));
    const formEdit = document.getElementById('formEditDinas');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const kode = this.getAttribute('data-kode');

            // Set Action URL Form (sesuaikan dengan rute admin.department.update)
            formEdit.action = `/admin/department/${id}`;

            // Isi input field
            document.getElementById('edit_nama_dinas').value = nama;
            document.getElementById('edit_kode_dinas').value = kode;

            // Tampilkan Modal
            editModal.show();
        });
    });
});
</script>
@endsection