@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Add Forms</h3>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Nama Form</label>
                <input type="text" class="form-control form-control-lg" placeholder="Nama Form">
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Form</label>
                <input type="text" class="form-control form-control-lg" placeholder="Nama Form">
            </div>

            <div class="mb-3" id="form-fields">
                <div class="mb-3 form-group d-flex justify-content-between align-items-center gap-2" data-question-id="0">
                    <input type="text" class="form-control form-control-lg" name="questions[]" placeholder="Pertanyaan"
                        required>
                    {{-- <button type="button" class="remove-btn btn btn-danger">Hapus</button> --}}
                </div>
            </div>
            <div class="d-flex justify-content-end align-items-center">
                <button type="button" id="add-btn" class="btn btn-primary btn-lg">Tambah Pertanyaan</button>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            let questionCount = 1;
            // Fungsi untuk menambah input baru
            $('#add-btn').click(function() {
                $('#form-fields').append(`
                    <div class="mb-3 form-group d-flex justify-content-between align-items-center gap-2" data-question-id="${questionCount}">
                        <input type="text" class="form-control form-control-lg" name="questions[]" placeholder="Pertanyaan"
                            required>
                        <button type="button" class="remove-btn btn btn-danger">Hapus</button>
                    </div>
                `);

                questionCount++;
            });

            // Fungsi untuk menghapus input
            $(document).on('click', '.remove-btn', function() {
                const groupId = $(this).parent('.form-group').data('question-id');
                if (confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')) {
                    $(this).parent('.form-group').remove();
                }
            });
        });
    </script>
@endpush
