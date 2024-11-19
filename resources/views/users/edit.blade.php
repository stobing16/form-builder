@extends('layouts.admin')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Edit User</h3>
    </div>
    <div class="alert alert-danger d-none" id="error-messages"></div> <!-- Div for error messages -->
    <form id="edit-user" action="{{ route('users.update', $user->id) }}">
        @method('PUT')
        <div class="card mb-3">
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label"><small>Username</small></label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $user->name) }}" placeholder="Username ...">
                </div>
                <div class="mb-4">
                    <label class="form-label"><small>Email</small></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" placeholder="Email ...">
                </div>
                <div class="mb-4">
                    <div class="mb-3 gap-2">
                        <label class="form-label"><small>Role</small></label>
                        <select id="role" name="role" class="form-control">
                            <option value="">Select Role</option>
                            <option value="super-admin" @if (old('role', $user->roles[0]->name) == 'super-admin') {{ 'selected' }} @endif>Super Admin</option>
                            <option value="admin" @if (old('role', $user->roles[0]->name) == 'admin') {{ 'selected' }} @endif>Admin</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 align-items-center">
            <div id="loading" class="spinner-border text-primary ms-2" style="display: none;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <button type="submit" class="btn btn-lg btn-primary" id="submit-btn">
                Update
            </button>
        </div>
    </form>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('#edit-user').validate({
                rules: {
                    'username': {
                        required: true,
                        minlength: 3
                    },
                    'email': {
                        required: true,
                        email: true
                    },
                    'role': {
                        required: true,
                    },
                },
                messages: {
                    'username': {
                        required: "Nama Form wajib diisi.",
                        minlength: "Nama Form harus lebih dari 3 karakter."
                    },
                    'email': {
                        required: "Form wajib diisi.",
                        email: "Form harus berupa email format."
                    },
                    'role': {
                        required: "Form wajib diisi.",
                    },
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

            $('#edit-user').on('submit', function(e) {
                e.preventDefault()

                var formData = $(this).serialize();
                $.ajax({
                    url: $(this).attr('action'), // URL action form
                    type: 'PUT',
                    data: formData + '&_method=PUT',
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
                            window.location.href = '{{ route('users') }}';
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
            })
        })
    </script>
@endpush
