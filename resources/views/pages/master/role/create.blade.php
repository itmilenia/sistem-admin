@extends('layouts.app')

@section('title', 'Tambah Peran Baru')

@section('content')
    <x-alert />

    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Manajemen User</a></li>
                <li class="breadcrumb-item"><a href="{{ route('master-role.index') }}">Master Peran</a></li>
                <li class="breadcrumb-item">Tambah Baru</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <form action="{{ route('master-role.store') }}" method="POST" id="createRoleForm">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title"><i class="feather-plus-square me-2"></i>Formulir Peran Baru</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6>Informasi Peran</h6>
                                    <p class="text-muted small">Masukkan nama unik untuk role baru. Gunakan huruf kecil dan
                                        underscore.
                                    </p>
                                    <hr>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Peran <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}"
                                            placeholder="Contoh: staff_gudang" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6>Hak Akses (Permissions)</h6>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="check-all-permissions">
                                            <label class="form-check-label" for="check-all-permissions">
                                                Pilih Semua
                                            </label>
                                        </div>
                                    </div>
                                    <p class="text-muted small">Pilih hak akses yang akan dimiliki oleh peran ini.</p>
                                    <hr>
                                    <div class="row">
                                        @foreach ($permissions as $permission)
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input permission-checkbox" type="checkbox"
                                                        name="permissions[]" value="{{ $permission->name }}"
                                                        id="perm-{{ $permission->id }}">
                                                    <label class="form-check-label" for="perm-{{ $permission->id }}">
                                                        {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-save me-2"></i> Simpan
                            </button>
                            <a href="{{ route('master-role.index') }}" class="btn btn-danger">Batal</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('check-all-permissions').addEventListener('click', function(event) {
            let checkboxes = document.querySelectorAll('.permission-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = event.target.checked;
            });
        });
    </script>

    {{-- Konfirmasi simpan --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createRoleForm');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Tambah Peran Baru',
                    text: "Pastikan data yang dipilih sudah benar.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, tambah!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
