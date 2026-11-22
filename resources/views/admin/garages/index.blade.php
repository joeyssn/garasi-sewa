@extends('layouts.app')
@section('title', 'Garages')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>Garages</h4>
    <a href="{{ route('admin.garages.create') }}" class="btn btn-primary">+ Create Garages</a>
</div>
<form class="mb-3 d-flex gap-2">
    <select name="type" class="form-select w-auto">
        <option value="">All types</option>
        @foreach($types as $t)<option value="{{ $t->value }}" @selected(request('type') === $t->value)>{{ $t->label() }}</option>@endforeach
    </select>
    <button class="btn btn-outline-secondary">Filter</button>
</form>
<table class="table table-bordered bg-white">
    <thead><tr><th>Code</th><th>Type</th><th class="text-end">Daily</th><th class="text-end">Monthly</th><th class="text-end">Per kWh</th><th>Status</th></tr></thead>
    <tbody>
    @foreach($garages as $g)
        <tr>
            <td>{{ $g->code }}</td>
            <td>{{ $g->type->label() }}</td>
            <td class="text-end">Rp {{ number_format($g->daily_price, 0, ',', '.') }}</td>
            <td class="text-end">Rp {{ number_format($g->monthlyPrice(), 0, ',', '.') }}</td>
            <td class="text-end">{{ $g->electricity_price_per_kwh ? 'Rp ' . number_format($g->electricity_price_per_kwh, 0, ',', '.') : '-' }}</td>
            <td><span class="badge {{ $g->status->value === 'used' ? 'bg-warning text-dark' : 'bg-success' }}">{{ $g->status->value }}</span></td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $garages->links() }}
@endsection