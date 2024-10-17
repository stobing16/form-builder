@extends('layouts.base')

@section('body')
    @yield('content')
@endsection

@push('css')
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
@endpush
