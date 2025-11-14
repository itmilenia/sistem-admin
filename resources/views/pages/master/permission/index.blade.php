@extends('layouts.app')

@section('title', 'Master Hak Akses')

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
                        <h5 class="card-title">Daftar Hak Akses (Permissions)</h5>
                        <div class="card-header-action">
                            <a href="{{ route('master-permission.create') }}" class="btn btn-primary"
                                data-bs-toggle="tooltip" title="Tambah Hak Akses Baru">
                                <i class="feather-plus-circle me-2"></i> Tambah Hak Akses
                            </a>
                        </div>
                    </div>
                    <div class="card-body custom-card-action p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 table-bordered" id="table-permission">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;" class="text-center">#</th>
                                        <th class="text-center">Nama Hak Akses</th>
                                        <th class="text-center">Guard</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($permissions as $permission)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td><span class="fw-bold">{{ $permission->name }}</span>
                                            </td>
                                            <td class="text-center"><span
                                                    class="badge bg-secondary">{{ $permission->guard_name }}</span></td>
                                            <td>
                                                <div class="hstack gap-2 justify-content-center">
                                                    <a href="{{ route('master-permission.edit', $permission->id) }}"
                                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                                        title="Edit Hak Akses">
                                                        <i class="feather-edit-2"></i>
                                                    </a>
                                                    {{-- <form action="{{ route('master-permission.destroy', $permission->id) }}"
                                                        method="POST" class="d-inline deletePermissionForm">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            data-bs-toggle="tooltip" title="Hapus Hak Akses">
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
            $('#table-permission').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });
        });
    </script>

    {{-- Konfirmasi hapus --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deletePermissionForm = document.querySelectorAll('.deletePermissionForm');
            deletePermissionForm.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Hapus Hak Akses',
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
