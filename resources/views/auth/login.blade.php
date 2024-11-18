@section('title', 'Login Page')
@extends('layouts.main')

@section('content')
    <section class="vh-100" style="background-color: rgb(236, 236, 236);">
        <div class="container h-100">
            <div class="row d-flex align-items-center justify-content-center h-100">
                <div class="col-md-10 col-lg-5 col-xl-5">
                    <div class="card shadow"> <!-- Background biru terang dan shadow -->
                        <div class="card-body">
                            <h2 class="text-center mb-6">Login Page</h2>
                            <form id="login-form">
                                <!-- Email input -->
                                <div data-mdb-input-init class="form-outline mb-4">
                                    <input type="email" name="email" id="email" class="form-control form-control-lg" required />
                                    <label class="form-label" for="email">Email address</label>
                                </div>

                                <!-- Password input -->
                                <div data-mdb-input-init class="form-outline mb-4">
                                    <input type="password" name="password" id="password" class="form-control form-control-lg" required />
                                    <label class="form-label" for="password">Password</label>
                                </div>

                                <!-- Submit button -->
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@push('css')
    <style>
        .divider:after,
        .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
        }
    </style>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $('#login-form').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                $.ajax({
                    url: "{{ route('login') }}", // Ensure this route matches your login route
                    method: 'POST',
                    data: $(this).serialize(), // Serialize the form data
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                            .attr('content') // Add CSRF token to the headers
                    },
                    success: function() {
                        // Handle successful login
                        Swal.fire({
                            title: 'Login Successful!',
                            text: 'You will be redirected shortly.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '/forms'; // Redirect user
                        });
                    },
                    error: function(xhr) {
                        // Handle errors
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';

                        if (errors) {
                            for (const [key, value] of Object.entries(errors)) {
                                errorMsg += value.join(', ') +
                                    '\n'; // Concatenate error messages
                            }
                        } else {
                            errorMsg = 'An unexpected error occurred. Please try again.';
                        }

                        Swal.fire({
                            title: 'Login Failed!',
                            text: errorMsg,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
@endpush
