@extends('layouts.base')

@section('body')
    @include('components.sidebar')

    <div class="navbar-container">
        @include('components.navbar')

        <div class="container content mt-2">
            @yield('content')
        </div>
    </div>
@endsection

@push('css')
    <style>
        body {
            display: flex;
            height: 100vh;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
        }

        .content {
            padding: 20px;

            @media (max-width: 768px) {
                margin-left: 0;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $('.toggle-sidebar').on('click', function() {
                if ($(window).width() <= 768) { // Cek lebar layar
                    $('#sidebar').toggleClass('active');
                    $('.content').toggleClass('active');
                    $('.toggle-sidebar').toggleClass('active');

                    if ($('#sidebar').hasClass('active')) {
                        // Sidebar aktif, lakukan tindakan di sini
                        $('.hamburger').hide(); // Sembunyikan ikon hamburger
                        $('.close').show(); // Tampilkan ikon close
                    } else {
                        // Sidebar tidak aktif
                        $('.hamburger').show(); // Tampilkan ikon hamburger
                        $('.close').hide(); // Sembunyikan ikon close
                    }
                }
            })
        });
    </script>
@endpush
