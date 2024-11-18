@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="fw-bold mb-3">{{ $form->title }}</h4>
            <p class="mb-3">{!! $form->description !!}</p>
            <hr class="pb-2">

            <form method="POST" id="responses" action="{{ route('forms.preview.store', $form->unique_url) }}">

                <div class="mb-4">
                    <label class="form-label fw-bold">Nama</label>
                    <input type="text" name="name" class="form-control form-control-lg" placeholder="Nama">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="Email">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Phone Number</label>
                    <input type="text" name="phone" class="form-control form-control-lg" placeholder="Phone">
                </div>

                <hr class="pb-2">
                @foreach ($form->questions as $question)
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
                    name: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    @foreach ($form->questions as $question)
                        @if ($question->is_required)
                            '{{ $form->unique_url }}[{{ $question->id }}]': {
                                required: true
                            },
                        @endif
                    @endforeach
                },
                messages: {
                    name: {
                        required: "Harap isi Form ini"
                    },
                    email: {
                        required: "Harap isi Form ini",
                        email: "Value Form harus dalam format email"
                    },
                    @foreach ($form->questions as $question)
                        @if ($question->is_required)
                            '{{ $form->unique_url }}[{{ $question->id }}]': {
                                required: "Harap isi pertanyaan ini."
                            },
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

            $('#responses').on('submit', function(e) {
                e.preventDefault()

                if ($(this).valid()) { // Memeriksa validasi
                    const actionUrl = $(this).attr('action');
                    const formData = $(this).serializeArray();
                    console.log(formData)

                    // $.ajax({
                    //     url: actionUrl,
                    //     type: 'POST',
                    //     data: formData,
                    //     // contentType: 'application/json',
                    //     headers: {
                    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    //     },
                    //     success: function(response) {
                    //         Swal.fire({
                    //             icon: 'success',
                    //             title: 'Success!',
                    //             text: response.message,
                    //             timer: 1500,
                    //             showConfirmButton: false
                    //         }).then(() => {
                    //             // Reset form setelah sukses
                    //             $('#responses')[0].reset();

                    //             // Reset validasi tampilan
                    //             $('#responses').find('.is-invalid').removeClass('is-invalid');
                    //             $('#responses').find('.valid').removeClass('valid');
                    //             $('#responses').find('.error-message').empty();
                    //         });
                    //     },
                    //     error: function(xhr, status, error) {
                    //         var errors = xhr.responseJSON.errors;
                    //         var errorMessage = '<ul>';
                    //         $.each(errors, function(key, value) {
                    //             errorMessage += '<li>' + value[0] + '</li>'; // Ambil pesan error pertama
                    //         });
                    //         errorMessage += '</ul>';
                    //         $('#error-messages').html(errorMessage).removeClass('d-none');
                    //     }
                    // });
                }
            })
        })
    </script>
@endpush
