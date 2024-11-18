@extends('layouts.admin')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Edit Pertanyaan</h3>
    </div>
    <div class="alert alert-danger d-none" id="error-messages"></div> <!-- Div for error messages -->

    <div class="card">
        <div class="card-body">
            <form id="edit-questions" action="{{ route('questions.update', $question->id) }}">
                @csrf
                @method('PUT')

                <label class="form-label fw-bold">Pertanyaan</label>
                <div class="mb-3" id="form-fields">
                    <div class="row mb-3 form-group" data-question-id="{{ $question->id }}">
                        <div class="col-9">
                            <input type="text" class="form-control form-control-lg" name="name" value="{{ old('question', $question->question) }}" placeholder="Pertanyaan" required>
                        </div>
                        <div class="col-3 mb-5">
                            <select class="form-select question-type" name="type">
                                <option value="">Form Type</option>
                                @foreach ($question_type as $type)
                                    <option value="{{ $type->id }}" data-has-options="{{ $type->has_options }}" {{ $type->id == $question->question_type_id ? 'selected' : '' }}>
                                        {{ $type->label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-4 row {{ $question->catatan ?? 'd-none' }} note-container" id="note-{{ $question->id ?? '0' }}">
                            <div class="col-11">
                                <label for="" class="form-label">Catatan</label>
                                <input type="text" class="form-control form-control-lg" value="{{ old('catatan', $question->catatan) }}" name="catatan" placeholder="Catatan">
                            </div>
                            <div class="col-1 align-self-end">
                                <button type="button" class="btn btn-sm btn-danger remove-note">
                                    <i class="bi bi-trash fs-6"></i>
                                </button>
                            </div>
                        </div>

                        <div class="options-container mb-3" id="options-{{ $question->id ?? '0' }}" style="{{ !is_null($question->options) ? 'display: block;' : 'display: none;' }}">
                            <label class="form-label fw-bold">Options</label>
                            @if (!is_null($question->options))
                                @foreach (json_decode($question->options, true) as $option)
                                    <div class="mb-4 options-form-group row">
                                        <div class="col-11">
                                            <input type="text" class="form-control" name="options[]" value="{{ $option }}" placeholder="Opsi">
                                        </div>
                                        @if ($loop->iteration > 1)
                                            <button type="button" class="btn btn-danger col-1 btn-sm remove-option"><i class="bi bi-trash"></i></button>
                                        @else
                                            <button type="button" class="btn btn-sm col-1 btn-secondary add-option">
                                                <i class="bi bi-plus fs-6"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="mb-4 options-form-group row">
                                    <div class="col-11">
                                        <input type="text" class="form-control" name="options[]" placeholder="Opsi">
                                    </div>
                                    <button type="button" class="btn btn-sm col-1 btn-secondary add-option">
                                        <i class="bi bi-plus fs-6"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_required" value="yes" id="flexCheckDefault{{ $question->id }}" {{ $question->is_required ? 'checked' : '' }}>
                                <label class="form-check-label" for="flexCheckDefault{{ $question->id }}">
                                    Wajib Diisi
                                </label>
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary add-note {{ $question->catatan ? 'd-none' : '' }}">
                                Add Note
                            </button>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-lg btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            const optionsContainerSelector = '.options-container';

            $('.question-type').each(function() {
                const hasOptions = $(this).find('option:selected').data('has-options');
                const questionId = $(this).closest('.form-group').data('question-id');
                const optionsContainer = $(`#options-${questionId}`);
            });

            // Show/hide options based on selected question type
            $(document).on('change', '.question-type', function() {
                const hasOptions = $(this).find('option:selected').data('has-options');
                const questionId = $(this).closest('.form-group').data('question-id');
                const optionsContainer = $(optionsContainerSelector + `#options-${questionId}`);

                // optionsContainer.toggle(hasOptions);
                if (hasOptions) {
                    optionsContainer.show(); // Sembunyikan jika opsi tidak diperlukan
                    optionsContainer.find('input').each(function() {
                        const oldValue = $(this).data('old-value');
                        if (oldValue) $(this).val(oldValue);
                    });
                } else {
                    optionsContainer.hide();
                }
            });

            function getOptionInputHTML() {
                return `
                <div class="mb-4 row options-form-group">
                    <div class="col-11">
                        <input type="text" class="form-control" name="options[]" placeholder="Opsi">
                    </div>
                    <button type="button" class="btn btn-danger col-1 btn-sm remove-option"><i class="bi bi-trash"></i></button>
                </div>`;
            }

            $(document).on('click', '.add-option', function() {
                const questionId = $(this).closest('.form-group').data('question-id');
                const optionsContainer = $(`#options-${questionId}`);

                optionsContainer.append(getOptionInputHTML());
            });

            $(document).on('click', '.remove-option', function() {
                $(this).closest('.options-form-group').remove();
            });

            // Fungsi untuk menambah opsi baru
            $(document).on('click', '.add-note', function() {
                const questionId = $(this).closest('.form-group').data('question-id');
                const noteContainer = $('#note-' + questionId);

                noteContainer.removeClass('d-none');

                // Aturan validasi untuk opsi baru
                noteContainer.find(`input[name="catatan"]`).rules("add", {
                    required: true,
                });
            });

            // Fungsi untuk menghapus opsi
            $(document).on('click', '.remove-note', function() {
                const questionId = $(this).closest('.form-group').data('question-id');
                const noteContainer = $('#note-' + questionId);

                noteContainer.addClass('d-none');
                $('.add-note').removeClass('d-none');
                noteContainer.find(`input[name="catatan"]`).val('').rules("remove");
            });

            // Custom validation rule for at least one option
            $.validator.addMethod("atLeastOneOption", function(value, element) {
                const questionId = $(element).closest('.form-group').data('question-id');
                const options = $(`[name="options[]"]`);
                return options.length > 0 && options.toArray().some(input => $(input).val().trim() !== '');
            }, "Setidaknya satu opsi harus diisi.");

            // Form validation rules
            $('#edit-questions').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 5
                    },
                    type: {
                        required: true
                    },
                    'options[]': {
                        atLeastOneOption: true
                    }
                },
                messages: {
                    name: {
                        required: "Pertanyaan wajib diisi.",
                        minlength: "Nama Pertanyaan harus lebih dari 5 karakter."
                    },
                    type: {
                        required: "Tipe pertanyaan wajib dipilih."
                    },
                    catatan: {
                        required: "Catatan Wajib Diisi."
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

            $('#edit-questions').on('submit', function(e) {
                e.preventDefault();

                if ($(this).valid()) {
                    let formData = $(this).serializeArray();
                    const structuredData = {
                        options: []
                    };

                    formData.forEach(item => {
                        if (typeRequiresOptions(structuredData.type) && item.name === "options[]") {
                            structuredData.options.push(item.value);
                        } else {
                            structuredData[item.name] = item.value
                        }
                    });

                    // Handle option validation
                    if (structuredData.options.length === 0 || structuredData.options.every(opt => opt === "")) {
                        structuredData.options = null; // Set to null if no valid options
                    }

                    // console.log(structuredData);

                    $.ajax({
                        url: $(this).attr('action'), // URL action form
                        type: 'PUT',
                        data: JSON.stringify(structuredData),
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '{{ route('forms.show', ['id' => $question->form_id]) }}';
                            });
                        },
                        error: function(xhr) {
                            // Tangani error
                            var errors = xhr.responseJSON.errors;
                            var errorMessage = '<ul>';
                            $.each(errors, function(key, value) {
                                errorMessage += '<li>' + value[0] + '</li>'; // Ambil pesan error pertama
                            });
                            errorMessage += '</ul>';
                            $('#error-messages').html(errorMessage).removeClass('d-none');
                        }
                    });
                }
            })

            // Helper function to determine if a type requires options
            function typeRequiresOptions(type) {
                return [3, 4, 5].includes(parseInt(type));
            }
        })
    </script>
@endpush
