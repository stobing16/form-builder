@section('title', 'Response')
@extends('layouts.admin')
@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Response</h3>
            {{-- <a href="{{ route('forms.create') }}" type="button" class="btn btn-primary">
                Add Forms
            </a> --}}
        </div>
        <div class="card">
            <div class="card-body card-dashboard">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="forms-table">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Forms</th>
                                <th>Tanggal Publish</th>
                                <th>Jumlah Response</th>
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
                ajax: "{{ route('response') }}", // Mengarah ke route yang sama
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
                        data: 'published_at',
                        name: 'published_at'
                    },
                    {
                        data: 'total_response',
                        name: 'total_response'
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
                window.location.href = "{{ route('response.show', ':id') }}".replace(':id', id);
            })

            // $(document).on('click', '.edit', function() {
            //     const id = $(this).data('id');
            //     window.location.href = "{{ route('forms.edit', ':id') }}".replace(':id', id);
            // })
        });
    </script>
@endpush
