@extends('layouts.app')

@section('title', 'Master User')

@section('content')
    <x-alert />

    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">@yield('title')</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Manajemen User</a></li>
                <li class="breadcrumb-item">@yield('title')</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Daftar User Sistem</h5>
                        <div class="card-header-action">

                            {{-- Tombol Aksi --}}
                            <div class="d-flex gap-2">
                                <a href="{{ route('master-user.sync') }}" class="btn btn-info" id="btnSync"
                                    data-bs-toggle="tooltip" title="Sinkronkan data user dengan data HRD"><i
                                        class="feather-refresh-cw me-2"></i>
                                    Sync Data</a>
                                <a href="{{ route('master-user.create') }}" class="btn btn-primary" data-bs-toggle="tooltip"
                                    title="Tambah User Baru">
                                    <i class="feather-plus-circle me-2"></i> Tambah User
                                </a>
                            </div>

                        </div>
                    </div>
                    <div class="card-body custom-card-action p-0">

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0" id="user-list">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 5%;" class="text-center">#</th>
                                        <th scope="col" class="text-center">Nama Karyawan</th>
                                        <th scope="col" class="text-center">Jabatan</th>
                                        <th scope="col" class="text-center">Divisi</th>
                                        <th scope="col" class="text-center">Role</th>
                                        <th scope="col" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($employees as $employee)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="hstack gap-3">
                                                    <div>
                                                        <span class="d-block fw-bold">{{ $employee->Nama }}</span>
                                                        <span class="fs-12 text-muted">ID: {{ $employee->ID }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $employee->Jabatan ?? '-' }}</td>
                                            <td>{{ $employee->Divisi ?? '-' }}</td>
                                            <td class="text-center">
                                                @foreach ($employee->roles as $role)
                                                    @php
                                                        switch ($role->name) {
                                                            case 'admin_pusat':
                                                                $badgeClass = 'bg-success'; // hijau
                                                                $label = 'Admin Pusat';
                                                                break;

                                                            case 'admin_cabang':
                                                                $badgeClass = 'bg-primary'; // biru muda
                                                                $label = 'Admin Cabang';
                                                                break;

                                                            default:
                                                                $badgeClass = 'bg-secondary'; // abu-abu default
                                                                $label = ucwords(str_replace('_', ' ', $role->name));
                                                                break;
                                                        }
                                                    @endphp

                                                    <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <div class="hstack gap-2 justify-content-center">
                                                    <a href="{{ route('master-user.edit', $employee->ID) }}"
                                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                                        title="Edit User">
                                                        <i class="feather-edit-2"></i>
                                                    </a>
                                                    <form action="{{ route('master-user.destroy', $employee->ID) }}"
                                                        method="POST" class="d-inline delete-user-form">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            data-bs-toggle="tooltip" title="Nonaktifkan User">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#user-list').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });
        });
    </script>

    {{-- Konfirmasi Sinkronisasi --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnSync = document.getElementById('btnSync');
            if (!btnSync) return;

            btnSync.addEventListener('click', function(e) {
                e.preventDefault();

                const url = this.getAttribute('href');

                Swal.fire({
                    title: 'Sinkronisasi Data User',
                    text: 'Apakah Anda yakin ingin melakukan sinkronisasi data dengan HRD?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>

    {{-- Konfirmasi Delete --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tangkap semua form nonaktif user
            const deleteForms = document.querySelectorAll('.delete-user-form');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // cegah submit langsung

                    Swal.fire({
                        title: 'Nonaktifkan User?',
                        text: 'User ini akan dinonaktifkan dari sistem. Apakah Anda yakin?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Nonaktifkan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#EA4D4D',
                        cancelButtonColor: '#6C757D',
                        customClass: {
                            confirmButton: 'btn btn-danger swal-custom-btn',
                            cancelButton: 'btn btn-secondary swal-custom-btn'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
