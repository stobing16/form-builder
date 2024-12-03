$(document).ready(function(){
    $('#editor').summernote();

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
            if (selectedQuestionType ==  4) {
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
                atLeastOneOption: true // Tambahkan ini untuk validasi opsi
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
                            structuredData.questions[index].options.push(item.value);
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
})
