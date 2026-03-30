@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 mt-5">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4 text-dark">Nexus Login</h3>
                <form action="{{ url('/login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label text-secondary">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label text-secondary">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label text-secondary" for="remember">Remember me</label>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary fw-bold">Sign In</button>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ url('/register') }}" class="text-decoration-none">Create new tenant account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
