@extends('layouts.app')
@section('title', 'My Rentals')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>My Rentals</h4>
    <a href="{{ route('rentals.create') }}" class="btn btn-primary">+ New Rental</a>
</div>
<table class="table table-bordered bg-white">
    <thead><tr><th>Garage</th><th>Vehicle</th><th>Type</th><th>Start</th><th>End</th><th class="text-end">Total</th><th>Status</th></tr></thead>
    <tbody>
    @forelse($rentals as $r)
        <tr>
            <td>{{ $r->garage->code }}</td>
            <td>{{ $r->vehicle->type->label() }} – {{ $r->vehicle->brand_model }} {{ $r->vehicle->plate_number }}</td>
            <td>{{ $r->rental_type->label() }}{{ $r->days ? " ({$r->days} days)" : '' }}</td>
            <td>{{ $r->start_date->format('d/m/Y') }}</td>
            <td>{{ $r->end_date->format('d/m/Y') }}</td>
            <td class="text-end">Rp {{ number_format($r->total_cost + $r->extensions->sum('additional_cost'), 0, ',', '.') }}</td>
            <td>
                @if($r->end_date->gte(today()))
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-secondary">Finished</span>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-center">No rentals yet.</td></tr>
    @endforelse
    </tbody>
</table>
{{ $rentals->links() }}
@endsection