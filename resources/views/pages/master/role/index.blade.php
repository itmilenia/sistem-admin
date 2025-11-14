@extends('layouts.app')

@section('title', 'Master Peran')

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
                        <h5 class="card-title">Daftar Peran Sistem</h5>
                        <div class="card-header-action">
                            <a href="{{ route('master-role.create') }}" class="btn btn-primary" data-bs-toggle="tooltip"
                                title="Tambah Role Baru">
                                <i class="feather-plus-circle me-2"></i> Tambah Peran
                            </a>
                        </div>
                    </div>
                    <div class="card-body custom-card-action p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0" id="table-role">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;" class="text-center">#</th>
                                        <th class="text-center">Nama Role</th>
                                        <th class="text-center">Guard</th>
                                        <th class="text-center">Jumlah User</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($roles as $role)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td><span
                                                    class="fw-bold">{{ ucwords(str_replace('_', ' ', $role->name)) }}</span>
                                            </td>
                                            <td class="text-center"><span
                                                    class="badge bg-secondary">{{ $role->guard_name }}</span></td>
                                            <td class="text-center">{{ $role->active_users_count }} User</td>
                                            <td>
                                                <div class="hstack gap-2 justify-content-center">
                                                    <a href="{{ route('master-role.edit', $role->id) }}"
                                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                                        title="Edit Role">
                                                        <i class="feather-edit-2"></i>
                                                    </a>
                                                    {{-- <form action="{{ route('master-role.destroy', $role->id) }}"
                                                        method="POST" class="d-inline deleteRoleForm">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            data-bs-toggle="tooltip" title="Hapus Role">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form> --}}
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
            $('#table-role').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });
        });
    </script>

    {{-- Konfirmasi hapus --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.deleteRoleForm');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Hapus Peran',
                        text: "Pastikan data yang dipilih sudah benar.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus!',
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
        });
    </script>
@endpush
