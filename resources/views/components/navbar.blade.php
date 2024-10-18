<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center gap-2 w-100">
            <div class="dropdown ms-auto">
                <button class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle icon-dropdown"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-person-fill"></i>
                            Profile
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider"> <!-- Separator -->
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" id="logout-link">
                            <i class="bi bi-box-arrow-right"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
            <button class=" toggle-sidebar d-lg-none" type="button">
                <i class="bi bi-list toggle-icon hamburger"></i> <!-- Hamburger Icon -->
                <i class="bi bi-x toggle-icon close"></i> <!-- X Icon -->
            </button>
        </div>
    </div>
</nav>

@push('css')
    <style>
        /* Navbar */
        .navbar {
            position: relative;
            z-index: 1001;
            box-shadow: 5px 5px #e2e2e2;

            @media (max-width: 768px) {
                z-index: 1;
                /* Reset margin untuk tampilan mobile */
            }
        }

        .navbar-container {
            flex: 1;
            transition: margin-left 0.3s ease;
            background: #e2e2e2;

            @media (max-width: 768px) {
                margin-left: 0;
                /* Reset margin untuk tampilan mobile */
            }

            @media (min-width: 768px) {
                margin-left: 250px;
                /* Margin untuk sidebar di desktop */
            }
        }

        /* Toggle Sidebar */
        .toggle-sidebar {
            background-color: transparent;
            border: none;
            padding: 0.5rem;
            outline: none;
            transition: background-color 0.2s;

            display: none;
            /* Sembunyikan tombol toggle di desktop */

            @media (max-width: 768px) {
                display: inline-block;
                /* Tampilkan di mobile */
            }

            &:hover {
                background-color: rgba(0, 123, 255, 0.1);
                /* Latar belakang saat hover */
                border-radius: 0.25rem;
            }

            &:focus {
                background-color: rgba(0, 123, 255, 0.2);
                /* Latar belakang saat fokus */
                outline: none;
            }
        }

        /* Toggle Icon */
        .toggle-icon {
            font-size: 1.2rem;
            transition: transform 0.2s;

            &.hamburger {
                display: inline;
                /* Tampilkan ikon hamburger */
            }

            &.close {
                display: none;
                /* Sembunyikan ikon close */
            }
        }

        /* Efek saat toggle sidebar aktif */
        .toggle-sidebar.active .toggle-icon.hamburger {
            display: none;
            /* Sembunyikan ikon hamburger */
        }

        .toggle-sidebar.active .toggle-icon.close {
            display: inline;
            /* Tampilkan ikon close */
        }

        /* Dropdown */
        .dropdown {
            margin-left: auto;
            /* Mendorong dropdown ke kanan */

            .dropdown-toggle {
                background: none;
                border: none;
                outline: none;
                font-size: 1.5rem;
                color: #0080ff;

                &:focus {
                    background-color: rgba(0, 123, 255, 0.1);
                    border-radius: 0.25rem;
                }
            }

            .dropdown-toggle::after {
                display: none;
            }

            .dropdown-menu {
                border-radius: 0.5rem;
                min-width: 200px;
                /* Sudut yang lebih lembut untuk dropdown */

                .dropdown-item {
                    transition: background-color 0.2s;
                    font-size: 0.9rem;
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;

                    &:hover {
                        background-color: #a2cdf7a1;
                    }
                }
            }
        }
    </style>
@endpush
@push('script')
    <script>
        $(document).ready(function() {
            $('#logout-link').on('click', function(e) {
                e.preventDefault(); // Prevent the default link behavior

                $.ajax({
                    url: '{{ route('logout') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // Include CSRF token
                    },
                    success: function(response) {
                        // Handle successful login
                        Swal.fire({
                            title: 'Logout Successful!',
                            text: 'You will be redirected shortly.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href =
                                '/login'; // Redirect to login page or homepage
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error during logout:", xhr.responseText);
                        Swal.fire({
                            title: 'Error during logout',
                            text: xhr.responseText,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
@endpush
