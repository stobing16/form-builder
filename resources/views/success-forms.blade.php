@extends('layouts.main')
@section('content')
    <div class="w-100 vh-100" style="background-color: aquamarine">
        <div class="custom-container py-5 d-flex justify-content-center">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h4 class="fw-bold mb-3">Thanks for Submitting your response</h4>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <style>
        .custom-container {
            width: 100%;
            padding: 15px;
        }

        @media (min-width: 600px) and (max-width: 1023px) {

            /* Tablet */
            .custom-container {
                width: 80%;
                padding: 20px;
                margin: 0 auto;
            }
        }

        @media (min-width: 1024px) {

            /* Desktop */
            .custom-container {
                width: 80%;
                padding: 25px;
                margin: 0 auto;
            }
        }
    </style>
@endpush
