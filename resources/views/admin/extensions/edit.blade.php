@extends('layouts.app')
@section('title', 'Extend Rental')
@section('content')
<h4>Extend rental of garage {{ $rental->garage->code }}</h4>
<div class="card card-body mb-3 col-md-8">
    <p class="mb-1">Tenant: <b>{{ $rental->user->name }}</b> · {{ $rental->vehicle->type->label() }} {{ $rental->vehicle->plate_number }}</p>
    <p class="mb-1">Period: {{ $rental->start_date->format('d/m/Y') }} – <b>{{ $rental->end_date->format('d/m/Y') }}</b></p>
    <p class="mb-0">Daily price: Rp {{ number_format($rental->garage->daily_price, 0, ',', '.') }}</p>
</div>
<form method="POST" action="{{ route('admin.extensions.update', $rental) }}" enctype="multipart/form-data" class="card card-body col-md-8">
    @csrf @method('PUT')
    <div class="mb-3"><label class="form-label">New end date</label>
        <input type="date" name="new_end_date" id="new_end_date" min="{{ $rental->end_date->copy()->addDay()->toDateString() }}" value="{{ old('new_end_date') }}" class="form-control" required></div>
    <div class="alert alert-info">Extra days: <b id="extra">-</b> · Remaining amount due: <b id="cost">-</b></div>
    <div class="mb-3"><label class="form-label">Proof of payment</label>
        <input type="file" name="payment_proof" accept="image/*" class="form-control" required></div>
    <button class="btn btn-primary">Save extension</button>
</form>
@endsection
@push('scripts')
<script>
document.getElementById('new_end_date').addEventListener('change', async e => {
    const res = await fetch(`{{ route('admin.extensions.quote', $rental) }}?new_end_date=${e.target.value}`, { headers: { Accept: 'application/json' } });
    const q = await res.json();
    document.getElementById('extra').textContent = res.ok ? q.extra_days : '-';
    document.getElementById('cost').textContent = res.ok ? 'Rp ' + Number(q.cost).toLocaleString('id-ID') : (q.message ?? '-');
});
</script>
@endpush