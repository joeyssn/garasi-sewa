@extends('layouts.app')
@section('title', 'New Rental')
@section('content')
<h4 class="mb-3">Rent a New Garage</h4>
<form method="POST" action="{{ route('rentals.store') }}" enctype="multipart/form-data" class="card card-body shadow-sm">
    @csrf
    <h6>Vehicle details</h6>
    <div class="row">
        <div class="col-md-3 mb-3"><label class="form-label">Vehicle type</label>
            <select name="vehicle_type" id="vehicle_type" class="form-select" required>
                <option value="">-- choose --</option>
                @foreach($vehicleTypes as $vt)
                    <option value="{{ $vt->value }}"
                        data-garages='@json(array_map(fn($g) => ['value' => $g->value, 'label' => $g->label()], $vt->allowedGarageTypes()))'
                        @selected(old('vehicle_type') === $vt->value)>{{ $vt->label() }}</option>
                @endforeach
            </select></div>
        <div class="col-md-3 mb-3" id="plate_group"><label class="form-label">Plate number</label>
            <input name="plate_number" value="{{ old('plate_number') }}" class="form-control"></div>
        <div class="col-md-3 mb-3"><label class="form-label">Brand and model</label>
            <input name="brand_model" value="{{ old('brand_model') }}" class="form-control" required></div>
        <div class="col-md-3 mb-3 d-none" id="battery_group"><label class="form-label">Battery capacity (kWh)</label>
            <input type="number" step="0.01" name="battery_capacity_kwh" id="battery" value="{{ old('battery_capacity_kwh') }}" class="form-control"></div>
    </div>

    <h6>Rental details</h6>
    <div class="row">
        <div class="col-md-3 mb-3"><label class="form-label">Rental type</label>
            <select name="rental_type" id="rental_type" class="form-select">
                <option value="daily" @selected(old('rental_type') !== 'monthly')>Daily</option>
                <option value="monthly" @selected(old('rental_type') === 'monthly')>Monthly</option>
            </select></div>
        <div class="col-md-2 mb-3" id="days_group"><label class="form-label">Days (max 30)</label>
            <input type="number" min="1" max="30" name="days" id="days" value="{{ old('days', 1) }}" class="form-control"></div>
        <div class="col-md-2 mb-3"><label class="form-label">Start date</label>
            <input type="date" value="{{ $today }}" class="form-control" readonly></div>
        <div class="col-md-2 mb-3"><label class="form-label">End date</label>
            <input type="date" id="end_date" class="form-control" readonly></div>
        <div class="col-md-3 mb-3"><label class="form-label">Garage type</label>
            <select id="garage_type" class="form-select"></select></div>
    </div>
    <div class="mb-3"><label class="form-label">Garages available today</label>
        <select name="garage_id" id="garage_id" class="form-select" required></select></div>

    <div class="alert alert-info">
        Rent: <b id="base">-</b> &nbsp;|&nbsp; Charging: <b id="charge">-</b> &nbsp;|&nbsp; <span class="fs-5">Total: <b id="total">-</b></span>
    </div>
    <div class="mb-3"><label class="form-label">Proof of payment (image)</label>
        <input type="file" name="payment_proof" accept="image/*" class="form-control" required></div>
    <button class="btn btn-primary">Save rental</button>
</form>
@endsection

@push('scripts')
<script>
const $ = id => document.getElementById(id);
const rupiah = n => 'Rp ' + Number(n).toLocaleString('id-ID');

// 1. Vehicle type chosen: show/hide fields and fill the allowed garage types.
function onVehicleChange() {
    const opt = $('vehicle_type').selectedOptions[0];
    const vt = $('vehicle_type').value;
    $('plate_group').classList.toggle('d-none', vt === 'bicycle');
    $('battery_group').classList.toggle('d-none', vt !== 'electric_car');
    const garages = opt && opt.dataset.garages ? JSON.parse(opt.dataset.garages) : [];
    $('garage_type').innerHTML = garages.map(g => `<option value="${g.value}">${g.label}</option>`).join('');
    loadGarages();
}

// 2. Garage type chosen: ask the server which garages are free today.
async function loadGarages() {
    const vt = $('vehicle_type').value, gt = $('garage_type').value;
    $('garage_id').innerHTML = '';
    if (!vt || !gt) return;
    const res = await fetch(`{{ route('rentals.available') }}?vehicle_type=${vt}&garage_type=${gt}`);
    const list = await res.json();
    $('garage_id').innerHTML = list.length
        ? list.map(g => `<option value="${g.id}">${g.code} — ${rupiah(g.daily_price)}/day</option>`).join('')
        : '<option value="">No garage available</option>';
    quote();
}

// 3. Anything changed: ask the server for the end date and the total cost.
async function quote() {
    const daily = $('rental_type').value === 'daily';
    $('days_group').classList.toggle('d-none', !daily);
    if (!$('garage_id').value) return;
    const params = new URLSearchParams({ garage_id: $('garage_id').value, rental_type: $('rental_type').value });
    if (daily) params.append('days', $('days').value);
    if ($('battery').value) params.append('battery_capacity_kwh', $('battery').value);
    const res = await fetch(`{{ route('rentals.quote') }}?${params}`, { headers: { Accept: 'application/json' } });
    if (!res.ok) return;
    const q = await res.json();
    $('end_date').value = q.end_date;
    $('base').textContent = rupiah(q.base_cost);
    $('charge').textContent = rupiah(q.charging_cost);
    $('total').textContent = rupiah(q.total_cost);
}

$('vehicle_type').addEventListener('change', onVehicleChange);
$('garage_type').addEventListener('change', loadGarages);
['garage_id', 'rental_type', 'days', 'battery'].forEach(id => $(id).addEventListener('input', quote));
onVehicleChange();
</script>
@endpush