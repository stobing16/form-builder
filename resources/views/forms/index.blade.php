@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Forms</h3>
            <a href="{{ route('forms.create') }}" type="button" class="btn btn-primary">
                Add Forms
            </a>
        </div>
        <div class="card">
            <div class="card-body card-dashboard">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="forms-table">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Forms</th>
                                <th>Status</th>
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
                ajax: "{{ route('forms') }}", // Mengarah ke route yang sama
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'is_active',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return (data == 1) ? '<span class="badge rounded-pill text-bg-primary">Active</span>' : '<span class="badge rounded-pill text-bg-danger">Inactive</span>'
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $(document).on('click', '.show', function() {
                const id = $(this).data('id');
                window.location.href = "{{ route('forms.show', ':id') }}".replace(':id', id);
            })

            $(document).on('click', '.edit', function() {
                const id = $(this).data('id');
                window.location.href = "{{ route('forms.edit', ':id') }}".replace(':id', id);
            })
        });
    </script>
@endpush

<style>
</style>
