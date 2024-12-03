@extends('layouts.admin')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Change Password</h3>
    </div>
    <div class="alert alert-danger d-none" id="error-messages"></div>

    <form id="change-password" action="{{ route('account.change-password.store') }}" method="POST">
        <div class="card mb-3">
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label fw-bold"><small>Password Lama</small></label>
                    <input type="password" name="old_password" class="form-control" placeholder="Password Lama ...">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold"><small>Password Baru</small></label>
                    <input id="new_password" type="password" name="new_password" class="form-control" placeholder="Password Baru ...">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold"><small>Masukkan Kembali Password Baru</small></label>
                    <input id="new_password_confirmation" type="password" name="new_password_confirmation" class="form-control" placeholder="Password Baru ...">
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
        $(document).ready(function() {
            $('#change-password').validate({
                rules: {
                    'old_password': {
                        required: true,
                        minlength: 6
                    },
                    'new_password': {
                        required: true,
                        minlength: 6
                    },
                    'new_password_confirmation': {
                        required: true,
                        minlength: 6,
                        equalTo: '[name="new_password"]'
                    },
                },
                messages: {

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

            $('#change-password').on('submit', function(e) {
                e.preventDefault();

                const form = $(this).serialize();
                if ($(this).valid()) {

                    $('#loading').show();
                    $('#submit-btn').prop('disabled', true);

                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: form,
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
                                    $('#change-password')[0].reset();
                                    window.location.href = '{{ route('forms') }}';
                                });

                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.error;
                                $('#change-password')[0].reset();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    html: errors,
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
            })
        })
    </script>
@endpush
