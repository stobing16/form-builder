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

            $('#editor').summernote();
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


            // Fungsi untuk menghapus input
            $(document).on('click', '.remove-btn', function() {
                if (confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')) {
                    $(this).closest('.form-group').parent().remove();
                }
            });

            // Show/hide options based on selected question type
            $(document).on('change', '.question-type', function() {
                const hasOptions = $(this).find('option:selected').data('has-options');
                const questionId = $(this).closest('.form-group').data('question-id');
                const optionsContainer = $('#options-' + questionId);
                const checkboxAdditionalForm = optionsContainer.find('.checkbox-additional-form');

                if (hasOptions) {
                    optionsContainer.show();

                    const selectedQuestionType = $(this).val();
                    if (selectedQuestionType == 4) {
                        checkboxAdditionalForm.show();
                    } else {
                        checkboxAdditionalForm.hide();
                    }
                } else {
                    optionsContainer.hide();
                    optionsContainer.find('input').val('');
                    checkboxAdditionalForm.hide();
                }

                // Update the data attribute for future changes
                $(this).data('previous-type', $(this).val());
            });


            // Aturan validasi untuk setidaknya satu opsi
            $.validator.addMethod("atLeastOneOption", function(value, element) {
                const questionId = $(element).closest('.form-group').data('question-id');
                const options = $(`[name="questions[${questionId}][options][]"]`);

                // Jika tidak ada opsi sama sekali, return false
                if (options.length === 0) {
                    return false; // Tidak ada opsi, tidak valid
                }

                return options.toArray().some(input => $(input).val().trim() !== '');
            }, "Setidaknya satu opsi harus diisi.");

            // Fungsi untuk menambah opsi baru
            $(document).on('click', '.add-option', function() {
                const questionId = $(this).closest('.form-group').data('question-id');
                const optionsContainer = $('#options-' + questionId);

                optionsContainer.append(`
                    <div class="mb-4 row options-form-group">
                        <div class="col-11">
                            <input type="text" class="form-control" name="questions[${questionId}][options][]" placeholder="Opsi">
                        </div>
                        <button type="button" class="btn btn-danger col-1 btn-sm remove-option"><i class="bi bi-trash"></i></button>
                    </div>
                `);

                // Aturan validasi untuk opsi baru
                optionsContainer.find(`input[name="questions[${questionId}][options][]"]`).rules("add", {
                    atLeastOneOption: true,
                });
            });

            // Fungsi untuk menghapus opsi
            $(document).on('click', '.remove-option', function() {
                $(this).closest('.options-form-group').remove();
            });

            // Fungsi untuk menambah opsi baru
            $(document).on('click', '.add-note', function() {
                const questionId = $(this).closest('.form-group').data('question-id');
                const noteContainer = $('#note-' + questionId);

                noteContainer.removeClass('d-none');

                // Aturan validasi untuk opsi baru
                noteContainer.find(`input[name="questions[${questionId}][catatan]"]`).rules("add", {
                    required: true,
                });
            });

            // Fungsi untuk menghapus opsi
            $(document).on('click', '.remove-note', function() {
                const questionId = $(this).closest('.form-group').data('question-id');
                const noteContainer = $('#note-' + questionId);

                noteContainer.addClass('d-none');
                noteContainer.find(`input[name="questions[${questionId}][catatan]"]`).val('').rules("remove");
            });

            // Aturan validasi untuk form
            $('#questions-form').validate({
                rules: {
                    'form_name': {
                        required: true,
                        minlength: 5
                    },
                    'questions[0][name]': {
                        required: true,
                        minlength: 5
                    },
                    'questions[0][type]': {
                        required: true
                    },
                    'questions[0][options][]': {
                        atLeastOneOption: true
                    }
                },
                messages: {
                    'form_name': {
                        required: "Nama Form wajib diisi.",
                        minlength: "Nama Form harus lebih dari 5 karakter."
                    },
                    'questions[0][name]': {
                        required: "Pertanyaan wajib diisi.",
                        minlength: "Nama Pertanyaan harus lebih dari 5 karakter."
                    },
                    'questions[0][type]': {
                        required: "Tipe pertanyaan wajib dipilih."
                    },
                    'questions[0][catatan]': {
                        required: "Catatan ini wajib diisi."
                    }
                },
                errorClass: "invalid-feedback",
                validClass: "valid",
                errorElement: "div",
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass("is-invalid").removeClass(validClass);
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass(errorClass).addClass(validClass);
                }
            });

            // Proses pengiriman form
            $('#questions-form').on('submit', function(e) {
                e.preventDefault();

                if ($(this).valid()) {
                    let formData = $(this).serializeArray();
                    let structuredData = {
                        questions: []
                    };

                    formData.forEach(function(item) {
                        if (item.name.startsWith('questions')) {
                            const match = item.name.match(/questions\[(\d+)]\[(\w+)]/);
                            if (match) {
                                const index = match[1];
                                const key = match[2];

                                if (!structuredData.questions[index]) {
                                    structuredData.questions[index] = {
                                        name: '',
                                        type: '',
                                        options: null
                                    };
                                }

                                if (key === 'options') {
                                    if (!structuredData.questions[index].options) {
                                        structuredData.questions[index].options = [];
                                    }
                                    if (item.value != '') {
                                        structuredData.questions[index].options.push(item.value);
                                    }
                                } else {
                                    structuredData.questions[index][key] = item.value;
                                }
                            }
                        } else if (item.name === 'form_name') {
                            structuredData.form_name = item.value;
                        } else if (item.name === 'description') {
                            structuredData.description = item.value;
                        }
                    });


                    // After looping, check each question's options
                    structuredData.questions.forEach(function(question) {
                        if (!question.options || question.options.length === 0) {
                            question.options = null;
                        } else {
                            question.options = question.options.length > 0 && question.options[0] != "" ? question.options : null;
                        }
                    });


                    // Tampilkan spinner dan disable tombol
                    $('#loading').show();
                    $('#submit-btn').prop('disabled', true);

                    const url = $(this).attr('action');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: JSON.stringify(structuredData),
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.message) {
                                // Gunakan SweetAlert2 untuk menampilkan pesan sukses
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = urlIndex;
                                });

                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                let errorMessages = Object.values(errors).flat();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    html: errorMessages.join('<br>'),
                                    confirmButtonText: 'Okay'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'An error occurred!',
                                    text: 'Please try again.',
                                    confirmButtonText: 'Okay'
                                });
                            }
                        },
                        complete: function() {
                            $('#loading').hide();
                            $('#submit-btn').prop('disabled', false);
                        }
                    });
                }
            });
        });
    </script>

    {{-- <script src="{{ asset('js/forms/create_form.js') }}"></script> --}}
@endpush
