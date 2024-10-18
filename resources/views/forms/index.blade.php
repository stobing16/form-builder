@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Forms</h3>
            <a href="{{ route('forms.create') }}" type="button" class="btn btn-primary">
                Add Data
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
                        data: 'form_name',
                        name: 'form_name'
                    }, // Ganti dengan nama kolom sesuai model Anda
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endpush

<style>
    .table {
        border-radius: 8px;
        overflow: hidden;
    }

    .table thead th {
        background-color: #007bff !important;
        color: black;
    }

    .table tbody tr:hover {
        background-color: #f1f1f1;
    }

    .modal-header {
        background-color: #007bff;
        color: white;
    }
</style>
