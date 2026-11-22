@extends('layouts.app')
@section('title', 'Create Garages')
@section('content')
<h4 class="mb-3">Create Garages (batch)</h4>
<form method="POST" action="{{ route('admin.garages.store') }}" class="card card-body shadow-sm col-md-6">
    @csrf
    <div class="mb-3"><label class="form-label">Garage type</label>
        <select name="type" id="type" class="form-select" required>
            @foreach($types as $t)
                <option value="{{ $t->value }}" @selected(old('type') === $t->value)>{{ $t->label() }} ({{ $t->prefix() }}xxx)</option>
            @endforeach
        </select></div>
    <div class="mb-3"><label class="form-label">Number of garages (max 999)</label>
        <input type="number" name="count" min="1" max="999" value="{{ old('count', 1) }}" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Daily price (Rp)</label>
        <input type="number" name="daily_price" min="1" value="{{ old('daily_price') }}" class="form-control" required>
        <small class="text-muted">Monthly price = 25 × daily price.</small></div>
    <div class="mb-3" id="kwh_group"><label class="form-label">Charging price per kWh (Rp)</label>
        <input type="number" step="0.01" name="electricity_price_per_kwh" value="{{ old('electricity_price_per_kwh') }}" class="form-control"></div>
    <button class="btn btn-primary">Save</button>
</form>
@endsection
@push('scripts')
<script>
const t = document.getElementById('type'), g = document.getElementById('kwh_group');
const toggle = () => g.classList.toggle('d-none', t.value !== 'electric');
t.addEventListener('change', toggle); toggle();
</script>
@endpush