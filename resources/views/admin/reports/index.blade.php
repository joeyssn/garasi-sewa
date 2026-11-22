@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<h4>Garage Rental Report</h4>
<form class="row g-2 mb-3">
    <div class="col-auto"><select name="mode" class="form-select">
        <option value="monthly" @selected($mode === 'monthly')>Per month (in a year)</option>
        <option value="daily" @selected($mode === 'daily')>Per day (in a month)</option>
        <option value="garage_type" @selected($mode === 'garage_type')>Per garage type</option>
        <option value="tenant" @selected($mode === 'tenant')>Per tenant</option>
    </select></div>
    <div class="col-auto"><input type="number" name="year" value="{{ $year }}" class="form-control" placeholder="Year"></div>
    <div class="col-auto"><input type="number" name="month" min="1" max="12" value="{{ $month }}" class="form-control" placeholder="Month"></div>
    <div class="col-auto"><button class="btn btn-primary">Show</button></div>
</form>
<table class="table table-bordered bg-white">
    <thead><tr><th>Period / Group</th><th class="text-end">Rentals</th><th class="text-end">Revenue</th></tr></thead>
    <tbody>
    @foreach($rows as $row)
        <tr><td>{{ $row['label'] }}</td><td class="text-end">{{ $row['count'] }}</td><td class="text-end">Rp {{ number_format($row['revenue'], 0, ',', '.') }}</td></tr>
    @endforeach
    </tbody>
    <tfoot><tr class="fw-bold"><td>Total</td><td class="text-end">{{ $rows->sum('count') }}</td><td class="text-end">Rp {{ number_format($rows->sum('revenue'), 0, ',', '.') }}</td></tr></tfoot>
</table>
@endsection