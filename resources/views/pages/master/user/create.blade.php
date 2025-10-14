@extends('layouts.app')

@section('title', 'Tambah User Baru')

@section('content')
    <x-alert />

    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Manajemen User</a></li>
                <li class="breadcrumb-item"><a href="{{ route('master-user.index') }}">Master User</a></li>
                <li class="breadcrumb-item">Tambah Baru</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <form action="{{ route('master-user.store') }}" method="POST" id="createUserForm">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title"><i class="feather-user-plus me-2"></i>Formulir Tambah User</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="employee-select" class="form-label">Pilih Karyawan <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="employee-select" name="IDs[]" multiple="multiple"
                                            required>
                                            @foreach ($newEmployees as $employee)
                                                <option value="{{ $employee->ID }}"
                                                    {{ is_array(old('IDs')) && in_array($employee->ID, old('IDs')) ? 'selected' : '' }}>
                                                    {{ $employee->Nama }} (ID: {{ $employee->ID }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('IDs')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @else
                                            <div class="form-text"><br>Anda bisa memilih lebih dari satu karyawan.</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="role-select" class="form-label">Tetapkan Role <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="role-select" name="role" required>
                                            <option value="" selected disabled>-- Pilih Role --</option>
                                            @foreach ($assignableRoles as $role)
                                                <option value="{{ $role->name }}"
                                                    {{ old('role') == $role->name ? 'selected' : '' }}>
                                                    {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-save me-2"></i> Simpan
                            </button>
                            <a href="{{ route('master-user.index') }}" class="btn btn-danger">Batal</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- select2 --}}
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 pada dropdown karyawan
            $('#employee-select').select2({
                placeholder: 'Cari dan pilih karyawan...',
                theme: 'bootstrap-5',
                allowClear: true,
                width: '100%'
            });

            // Inisialisasi Select2 pada dropdown role
            $('#role-select').select2({
                placeholder: '-- Pilih Role --',
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
    </script>

    {{-- Konfirmasi simpan --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createUserForm');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Tambah User Baru',
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
