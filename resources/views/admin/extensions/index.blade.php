@extends('layouts.app')
@section('title', 'Extend Rental')
@section('content')
<h4 class="mb-3">Garages rented today</h4>
<table class="table table-bordered bg-white">
    <thead>
        <tr><th>Garage</th><th>Tenant</th><th>Vehicle</th><th>Type</th><th>Start</th><th>End</th><th></th></tr>
    </thead>
    <tbody>
    @forelse($active as $r)
        <tr>
            <td>{{ $r->garage->code }}</td>
            <td>{{ $r->user->name }}</td>
            <td>{{ $r->vehicle->type->label() }} {{ $r->vehicle->plate_number }}</td>
            <td>{{ $r->rental_type->label() }}</td>
            <td>{{ $r->start_date->format('d/m/Y') }}</td>
            <td>{{ $r->end_date->format('d/m/Y') }}</td>
            <td><a href="{{ route('admin.extensions.edit', $r) }}" class="btn btn-sm btn-primary">Extend</a></td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-center text-muted">No garages are rented today.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
