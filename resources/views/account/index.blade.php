@extends('layouts.admin')
@section('content')
    <div class="d-md-flex justify-content-between align-items-center mb-3">
        <h3>Account</h3>
        <a href="{{ route('account.change-password') }}" type="button" class="btn btn-primary">
            Change Password
        </a>
    </div>
    <div class="container">
        <div class="row g-2">
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ ucfirst(auth()->user()->name) }}</h5>
                        <p class="card-text"><small>Email : {{ auth()->user()->email }}</small></p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Form yang dibuat</h5>
                        {{-- <p class="card-text fs-6">Email : {{ auth()->user()->email }}</p> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="card">
        <div class="card-body card-dashboard">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="forms-table">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Forms</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan diisi oleh DataTable -->
                    </tbody>
                </table>
            </div>
        </div>
    </div> --}}
@endsection
