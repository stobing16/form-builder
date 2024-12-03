@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Add Forms</h3>
    </div>
    <div class="alert alert-danger d-none" id="error-messages"></div> <!-- Div for error messages -->

    <form id="questions-form" action="{{ route('forms.store') }}">
        <div class="card mb-3">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label"><small>Nama Form</small></label>
                    <input type="text" name="form_name" class="form-control form-control-lg" placeholder="Nama Form">
                </div>
                <div class="mb-3">
                    <label class="form-label"><small>Description</small></label>
                    <textarea class="form-control form-control-lg" id="editor" name="description" rows="5"></textarea>
                </div>
            </div>
        </div>
        <div class="card mb-3 ">
            <div class="card-body">
                <label class="form-label fw-bold">Pertanyaan</label>
                <div class="mb-3" id="form-fields">
                    <div class="card mb-3 border border-info shadow p-2 rounded-5">
                        <div class="row mb-3 form-group card-body" data-question-id="0">
                            <div class="col-9">
                                <input type="text" class="form-control form-control-lg" name="questions[0][name]" placeholder="Pertanyaan">
                            </div>
                            <div class="col-3 mb-5">
                                <select class="form-select question-type" name="questions[0][type]">
                                    <option value="">Form Type</option>
                                    @foreach ($question_type as $type)
                                        <option value="{{ $type->id }}" data-has-options="{{ $type->has_options }}">
                                            {{ $type->label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-4 row d-none note-container" id="note-0">
                                <div class="col-11">
                                    <label for="" class="form-label">Catatan</label>
                                    <input type="text" class="form-control form-control-lg" name="questions[0][catatan]" placeholder="Catatan">
                                </div>
                                <div class="col-1 align-self-end">
                                    <button type="button" class="btn btn-sm btn-danger remove-note">
                                        <i class="bi bi-trash fs-6"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="options-container mb-3" id="options-0" style="display: none;">
                                <label class="form-label fw-bold">Options</label>
                                <div class="checkbox-additional-form mb-2" style="display: none;">
                                    <input class="form-check-input" type="checkbox" name="questions[0][has_additional_question]" value="yes" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        <small class="fw-semibold">Tambahkan Opsi Lainnya ...</small>
                                    </label>
                                    <p style="font-size: 9pt;" class="fw-light">akan menambahkan form input pilihan lainnya</p>
                                </div>
                                <div class="mb-4 options-form-group row">
                                    <div class="col-11">
                                        <input type="text" class="form-control" name="questions[0][options][]" placeholder="Opsi">
                                    </div>
                                    <button type="button" class="btn btn-sm col-1 btn-secondary add-option">
                                        <i class="bi bi-plus fs-6"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="questions[0][is_required]" value="yes" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Wajib Diisi
                                    </label>
                                </div>
                                <button type="button" class="btn btn-sm btn-secondary add-note">
                                    Add Note
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end align-items-center">
                    <button type="button" id="add-btn" class="btn btn-secondary btn-lg">Tambah Pertanyaan</button>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 align-items-center">
            <div id="loading" class="spinner-border text-primary ms-2" style="display: none;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <button type="submit" class="btn btn-lg btn-primary" id="submit-btn">
                Submit
            </button>
        </div>
    </form>
@endsection
@push('script')
    <script>
        let urlIndex = '{{ route('forms') }}'

        $(document).ready(function() {
            let questionCount = 1;

            // Fungsi untuk menambah input baru
            $('#add-btn').click(function() {
                $('#form-fields').append(`
                    <div class="card mb-3 border border-info shadow p-2 rounded-5">
                        <div class="row mb-3 form-group card-body" data-question-id="${questionCount}">
                            <div class="col-8">
                                <input type="text" class="form-control form-control-lg" name="questions[${questionCount}][name]" placeholder="Pertanyaan">
                            </div>
                            <div class="col-3 mb-5">
                                <select class="form-select question-type" name="questions[${questionCount}][type]">
                                    <option value="">Form Type</option>
                                    @foreach ($question_type as $type)
                                        <option value="{{ $type->id }}" data-has-options="{{ $type->has_options }}">{{ $type->label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-1">
                                <button type="button" class="remove-btn btn btn-danger"><i class="bi bi-trash"></i></button>
                            </div>
                            <div class="col-12 mb-4 row d-none note-container" id="note-${questionCount}">
                                <div class="col-11">
                                    <label for="" class="form-label">Catatan</label>
                                    <input type="text" class="form-control form-control-lg" name="questions[${questionCount}][catatan]" placeholder="Catatan">
                                </div>
                                <div class="col-1 align-self-end">
                                    <button type="button" class="btn btn-sm btn-danger remove-note">
                                        <i class="bi bi-trash fs-6"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="options-container mb-3" id="options-${questionCount}" style="display: none;">
                                <label class="form-label fw-bold">Options</label>
                                <div class="checkbox-additional-form mb-2" style="display: none;">
                                    <input class="form-check-input" type="checkbox" name="questions[${questionCount}][has_additional_question]" value="yes" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        <small class="fw-semibold">Tambahkan Opsi Lainnya ...</small>
                                    </label>
                                    <p style="font-size: 9pt;" class="fw-light">akan menambahkan form input pilihan lainnya</p>
                                </div>
                                <div class="mb-4 options-form-group row">
                                    <div class="col-11">
                                        <input type="text" class="form-control" name="questions[${questionCount}][options][]" placeholder="Opsi">
                                    </div>
                                    <button type="button" class="btn btn-sm col-1 btn-secondary add-option"><i class="bi bi-plus fs-6"></i></button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="questions[${questionCount}][is_required]" value="yes" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Wajib Diisi
                                    </label>
                                </div>
                                <button type="button" class="btn btn-sm btn-secondary add-note">
                                    Add Note
                                </button>
                            </div>
                        </div>
                    </div>
                `);

                // Aturan validasi untuk pertanyaan baru
                $(`[name="questions[${questionCount}][name]"]`).rules("add", {
                    required: true,
                    minlength: 5,
                    messages: {
                        required: "Pertanyaan wajib diisi.",
                        minlength: "Nama Pertanyaan harus lebih dari 5 karakter."
                    }
                });

                $(`[name="questions[${questionCount}][type]"]`).rules("add", {
                    required: true,
                    messages: {
                        required: "Tipe pertanyaan wajib dipilih."
                    }
                });

                questionCount++;
            });
        });
    </script>

    <script src="{{ asset('js/forms/create_form.js') }}"></script>
@endpush
