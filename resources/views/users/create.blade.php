@extends('layouts.admin')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Add User</h3>
    </div>
    <div class="alert alert-danger d-none" id="error-messages"></div>

    <form id="questions-form">
        <div class="card mb-3">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label"><small>Username</small></label>
                    <input type="text" name="username" class="form-control" placeholder="Username ...">
                </div>
                <div class="mb-3">
                    <label class="form-label"><small>Email</small></label>
                    <input type="email" name="email" class="form-control" placeholder="Email ...">
                </div>

                <div class="mb-3">
                    <div class="mb-3 gap-2">
                        <label class="form-label"><small>Role</small></label>
                        <select id="role" name="role" class="form-control">
                            <option value="">Select Role</option>
                            <option value="super-admin">Super Admin</option>
                            <option value="admin">Admin</option>
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
                Submit
            </button>
        </div>
    </form>
@endsection
