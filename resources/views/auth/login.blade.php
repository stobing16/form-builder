@section('title', 'Login Page')
@extends('layouts.main')

@section('content')
    <section class="vh-100" style="background-color: rgb(236, 236, 236);">
        <div class="container h-100">
            <div class="row d-flex align-items-center justify-content-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-6 offset-xl-1">
                    <div class="card shadow"> <!-- Background biru terang dan shadow -->
                        <div class="card-body">
                            <h2 class="text-center mb-6">Login Page</h2>
                            <form id="login-form">
                                <!-- Email input -->
                                <div data-mdb-input-init class="form-outline mb-4">
                                    <input type="email" name="email" id="email" class="form-control form-control-lg"
                                        required />
                                    <label class="form-label" for="email">Email address</label>
                                </div>

                                <!-- Password input -->
                                <div data-mdb-input-init class="form-outline mb-4">
                                    <input type="password" name="password" id="password"
                                        class="form-control form-control-lg" required />
                                    <label class="form-label" for="password">Password</label>
                                </div>

                                <!-- Submit button -->
                                {{-- data-mdb-button-init data-mdb-ripple-init --}}
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
                    url: '{{ route('login') }}', // Ensure this route matches your login route
                    method: 'POST',
                    data: $(this).serialize(), // Serialize the form data
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // Add CSRF token to the headers
                    },
                    success: function(response) {
                        // Handle successful login
                        window.location.href = response.redirect; // Redirect user
                    },
                    error: function(xhr) {
                        // Handle errors
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';
                        for (const [key, value] of Object.entries(errors)) {
                            errorMsg += value.join(', ') + '\n'; // Concatenate error messages
                        }
                        alert(errorMsg); // Display errors
                    }
                });
            });
        });
    </script>
@endpush
