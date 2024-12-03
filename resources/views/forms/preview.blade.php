@section('title', 'Preview Form')
@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="fw-bold mb-3">{{ $form->title }}</h4>
            <p class="mb-3">{!! $form->description !!}</p>
            <hr class="pb-2">

            <form method="POST" id="responses" action="#">
                @foreach ($questions as $question)
                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ $question->question }}</label>
                        <p class="mb-1" style="font-size: 10pt; margin-bottom: 0;">{{ isset($question->catatan) ? '*' . $question->catatan : '' }}</p>

                        @switch($question->question_type_id)
                            @case(1)
                                <input type="{{ $question->type->type }}" name="{{ $form->unique_url . '[' . $question->id . ']' }}" class="form-control form-control-lg" placeholder="{{ $question->question }}"
                                    @if ($question->is_required) required @endif>
                            @break

                            @case(2)
                                <textarea class="form-control form-control-lg" placeholder="{{ $question->question }}" name="{{ $form->unique_url . '[' . $question->id . ']' }}" rows="5" @if ($question->is_required) required @endif></textarea>
                            @break

                            @case(3)
                                @foreach (json_decode($question->options, true) as $option)
                                    <div class="form-check form-check-inline mt-2">
                                        <input class="form-check-input" type="{{ $question->type->type }}" name="{{ $form->unique_url . '[' . $question->id . ']' }}" value="{{ $option }}" @if ($question->is_required) required @endif>
                                        <label class="form-check-label">
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach
                            @break

                            @case(4)
                                @foreach (json_decode($question->options, true) as $option)
                                    <div class="form-check mt-2 form-check-inline">
                                        <input class="form-check-input" type="{{ $question->type->type }}" name="{{ $form->unique_url . '[' . $question->id . '][]' }}" value="{{ $option }}" id="flexCheckDefault">
                                        <label class="form-check-label" for="flexCheckDefault">
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach
                                @if ($question->has_additional_question)
                                    <div class="form-check mt-2 form-check-inline">
                                        <input class="form-check-input check-others" id="other-{{ $question->id }}" type="{{ $question->type->type }}" data-id="{{ $question->id }}" name="other-value-{{ $form->unique_url }}" value="">
                                        <label class="form-check-label" for="flexCheckDefault">
                                            Lainnya ...
                                        </label>
                                    </div>

                                    <!-- Form tambahan yang akan ditampilkan setelah checkbox diklik -->
                                    <div id="additional_answer_{{ $question->id }}" class="mt-2" style="display:none;">
                                        <label for="additional_answer" class="form-label">Lainnya :</label>
                                        <input type="text" id="additional_answer" class="form-control form-control-lg" name="{{ $form->unique_url . '[' . $question->id . '][additional_answer]' }}">
                                    </div>
                                @endif
                            @break

                            @case(5)
                                <select class="form-select" aria-label="Default select example" name="{{ $form->unique_url . '[' . $question->id . ']' }}" @if ($question->is_required) required @endif>
                                    <option selected>Select ...</option>
                                    @foreach (json_decode($question->options) as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            @break

                            @default
                        @endswitch

                        <!-- Tempat untuk menampilkan pesan error -->
                        <div class="error-message"></div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>

        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('#responses').validate({
                rules: {
                    @foreach ($questions as $question)
                        @if ($question->is_required)
                            "{{ $form->unique_url }}[{{ $question->id }}]": {
                                required: true
                            },

                            // Validasi untuk grup checkbox
                            @if ($question->question_type_id == 4)
                                '{{ $form->unique_url }}[{{ $question->id }}][]': {
                                    required: true
                                },
                            @endif

                            @if ($question->has_additional_question)
                                // Validasi untuk "Others"
                                '{{ $form->unique_url }}[{{ $question->id }}][additional_answer]': {
                                    required: function() {
                                        // Cek apakah "Others" dicentang
                                        return $('input[name="other-value-{{ $form->unique_url }}"]:checked').length > 0;
                                    }
                                },
                            @endif
                        @endif
                    @endforeach
                },
                messages: {
                    @foreach ($questions as $question)
                        @if ($question->is_required)
                            '{{ $form->unique_url }}[{{ $question->id }}]': {
                                required: "Harap isi pertanyaan ini."
                            },

                            // Validasi untuk grup checkbox
                            @if ($question->question_type_id == 4)
                                '{{ $form->unique_url }}[{{ $question->id }}][]': {
                                    required: "Harap pilih setidaknya satu opsi."
                                },
                            @endif

                            @if ($question->has_additional_question)
                                '{{ $form->unique_url }}[{{ $question->id }}][additional_answer]': {
                                    required: "Harap isi jawaban Anda jika memilih 'Lainnya'."
                                },
                            @endif
                        @endif
                    @endforeach
                },
                errorClass: "invalid-feedback",
                validClass: "valid",
                errorElement: "div",
                errorPlacement: function(error, element) {
                    // Menempatkan pesan error di bawah elemen input
                    error.appendTo(element.closest('.mb-4').find('.error-message'));
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass("is-invalid").removeClass(validClass);
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass(errorClass).addClass(validClass);
                }
            });

            $('.check-others').change(function() {
                const id = $(this).data('id')
                const additionalForm = $(`#additional_answer_${id}`);
                if ($(this).is(':checked')) {
                    additionalForm.show();
                } else {
                    additionalForm.hide();
                }
            });

            $('#responses').on('submit', function(e) {
                e.preventDefault()

                if ($(this).valid()) { // Memeriksa validasi
                    const actionUrl = $(this).attr('action');
                    const formData = $(this).serializeArray();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: "Test Submit Berhasil",
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Reset form setelah sukses
                        $('#responses')[0].reset();

                        // Reset validasi tampilan
                        $('#responses').find('.is-invalid').removeClass('is-invalid');
                        $('#responses').find('.valid').removeClass('valid');
                        $('#responses').find('.error-message').empty();
                    });
                }
            })
        })
    </script>
@endpush
