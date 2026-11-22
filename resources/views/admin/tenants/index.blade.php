@extends('layouts.app')
@section('title', 'Verify Registrants')
@section('content')
<h4>Registrants waiting for verification</h4>
<p class="text-muted">Check the NIK and name against the KTP before approving.</p>
<table class="table table-bordered bg-white">
    <thead><tr><th>NIK</th><th>Name</th><th>Phone</th><th>Email</th><th>Registered</th><th>Action</th></tr></thead>
    <tbody>
    @forelse($pending as $u)
        <tr>
            <td>{{ $u->nik }}</td><td>{{ $u->name }}</td><td>{{ $u->phone }}</td><td>{{ $u->email }}</td>
            <td>{{ $u->created_at->format('d/m/Y H:i') }}</td>
            <td class="d-flex gap-1">
                <form method="POST" action="{{ route('admin.tenants.approve', $u) }}">@csrf @method('PATCH')<button class="btn btn-success btn-sm">Approve</button></form>
                <form method="POST" action="{{ route('admin.tenants.reject', $u) }}">@csrf @method('PATCH')<button class="btn btn-danger btn-sm">Reject</button></form>
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center">No new registrants.</td></tr>
    @endforelse
    </tbody>
</table>
<h5 class="mt-4">Verification history</h5>
<table class="table table-sm table-bordered bg-white">
    <thead><tr><th>NIK</th><th>Name</th><th>Email</th><th>Status</th></tr></thead>
    <tbody>@foreach($others as $u)<tr><td>{{ $u->nik }}</td><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td>{{ $u->status->label() }}</td></tr>@endforeach</tbody>
</table>
@endsection