@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="row justify-content-center"><div class="col-md-5">
    <div class="card shadow-sm"><div class="card-body">
        <h4 class="mb-3">Login</h4>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3"><label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus></div>
            <div class="mb-3"><label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required></div>
            <button class="btn btn-primary w-100">Log in</button>
        </form>
        <p class="mt-3 mb-0 text-center">No account yet? <a href="{{ route('register') }}">Register as a tenant</a></p>
    </div></div>
</div></div>
@endsection