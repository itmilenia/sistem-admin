@extends('layouts.app')

@section('title', 'Edit User')

@push('styles')
    <style>
        .permission-group {
            margin-bottom: 1.5rem;
        }

        .permission-group-title {
            font-weight: 600;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
    </style>
@endpush

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
                <li class="breadcrumb-item">Edit</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <form action="{{ route('master-user.update', $employee->ID) }}" method="POST" id="edit-user-form">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="feather-edit me-2"></i>Edit User: {{ $employee->Nama }}
                            </h5>
                        </div>
                        <div class="card-body">
                            {{-- Bagian Edit Role --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <h6>User Role</h6>
                                    <p class="text-muted small">Ubah role utama untuk user ini. Mengubah role mungkin akan
                                        mengubah hak akses bawaannya.</p>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="role-select" class="form-label">Role <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="role-select" name="role" required>
                                            <option value="" disabled>-- Pilih Role --</option>
                                            @foreach ($assignableRoles as $role)
                                                <option value="{{ $role->name }}"
                                                    {{ $employee->hasRole($role->name) ? 'selected' : '' }}>
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

                            <hr class="my-4">

                            {{-- Bagian Edit Permissions --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <h6>Direct Permissions</h6>
                                    <p class="text-muted small">Berikan hak akses tambahan (direct permission) di luar yang
                                        sudah didapat dari role. Ini berguna untuk kasus-kasus khusus.</p>
                                </div>
                                <div class="col-md-8">
                                    @error('permissions')
                                        <div class="alert alert-danger small p-2">{{ $message }}</div>
                                    @enderror

                                    @php
                                        // Grouping permissions for better UI
                                        $groupedPermissions = $allPermissions->groupBy(function ($item) {
                                            return explode('_', $item->name)[0];
                                        });
                                    @endphp

                                    @foreach ($groupedPermissions as $groupName => $permissions)
                                        <div class="permission-group">
                                            <h6 class="permission-group-title">{{ ucfirst($groupName) }}</h6>
                                            <div class="row">
                                                @foreach ($permissions as $permission)
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="permissions[]" value="{{ $permission->name }}"
                                                                id="perm-{{ $permission->id }}"
                                                                {{ $employee->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="perm-{{ $permission->id }}">
                                                                {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
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

    <script>
        $(document).ready(function() {
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
        document.getElementById('edit-user-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;

            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Pastikan role dan permission yang dipilih sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, simpan!',
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
    </script>
@endpush
