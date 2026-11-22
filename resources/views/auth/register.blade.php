@extends('layouts.app')
@section('title', 'Register')
@section('content')
<div class="row justify-content-center"><div class="col-md-6">
    <div class="card shadow-sm"><div class="card-body">
        <h4 class="mb-3">Register as a tenant</h4>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-3"><label class="form-label">NIK (KTP number)</label>
                <input name="nik" value="{{ old('nik') }}" maxlength="16" inputmode="numeric" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Full name</label>
                <input name="name" value="{{ old('name') }}" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Phone number</label>
                <input name="phone" value="{{ old('phone') }}" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Email (username)</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required></div>
            <div class="row">
                <div class="col mb-3"><label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required></div>
                <div class="col mb-3"><label class="form-label">Repeat password</label>
                    <input type="password" name="password_confirmation" class="form-control" required></div>
            </div>
            <button class="btn btn-primary w-100">Register</button>
        </form>
    </div></div>
</div></div>
@endsection