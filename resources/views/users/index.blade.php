@extends('layouts.admin')
@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>User Management</h3>
            <a href="{{ route('users.create') }}" type="button" class="btn btn-primary">
                Add User
            </a>
        </div>

        <!-- Filter by Role -->
        <div class="mb-3 gap-2">
            <label for="roleFilter">Role : </label>
            <select id="roleFilter" class="form-control">
                <option value="">Select Role</option>
                <option value="super-admin">Super Admin</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="card">
            <div class="card-body card-dashboard">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="forms-table">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi oleh DataTable -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            var table = $('#forms-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('users') }}",
                    data: function(d) {
                        d.role = $('#roleFilter').val();
                    }
                },
                initComplete: function(settings, json) {
                    $('#dataTable_length').append('<label>&nbsp; App ID:</label>');
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#roleFilter').on('change', function() {
                table.draw();
            });

            $(document).on('click', '.edit', function() {
                const id = $(this).data('id');
                window.location.href = "{{ route('users.edit', ':id') }}".replace(':id', id);
            })
        });
    </script>
@endpush
