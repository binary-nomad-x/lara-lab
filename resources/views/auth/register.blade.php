@extends('layouts.app')

@section('title', 'Register Tenant')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 mt-3 mb-5">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4 text-dark">Register Enterprise</h3>
                <form action="{{ url('/register') }}" method="POST">
                    @csrf
                    <h5 class="mb-3 text-secondary">Tenant Information</h5>
                    <div class="mb-3">
                        <label class="form-label">Enterprise Name</label>
                        <input type="text" class="form-control" name="tenant_name" value="{{ old('tenant_name') }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Subdomain Choice</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="domain" value="{{ old('domain') }}" required>
                            <span class="input-group-text">.nexuseiams.com</span>
                        </div>
                    </div>

                    <h5 class="mb-3 text-secondary">Admin Account</h5>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="user_name" value="{{ old('user_name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-success fw-bold">Provision Setup & Register</button>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ url('/login') }}" class="text-decoration-none">Already have an account? Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
