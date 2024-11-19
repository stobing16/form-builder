@extends('layouts.admin')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Account</h3>
        <a href="{{ route('forms.create') }}" type="button" class="btn btn-primary">
            Change Password
        </a>
    </div>
    <div class="card" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title">Card title</h5>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
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
