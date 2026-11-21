<?php

namespace App\Http\Controllers\Admin;

use App\Enums\GarageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGarageBatchRequest;
use App\Models\Garage;
use App\Services\GarageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class GarageController extends Controller
{
    public function index(Request $request): View
    {
        $garages = Garage::query()
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->orderBy('type')->orderBy('code')
            ->paginate(25)->withQueryString();

        return view('admin.garages.index', ['garages' => $garages, 'types' => GarageType::cases()]);
    }

    public function create(): View
    {
        return view('admin.garages.create', ['types' => GarageType::cases()]);
    }

    public function store(StoreGarageBatchRequest $request, GarageService $service): RedirectResponse
    {
        $data = $request->validated();

        try {
            $created = $service->createBatch(
                GarageType::from($data['type']),
                (int) $data['count'],
                (float) $data['daily_price'],
                isset($data['electricity_price_per_kwh']) ? (float) $data['electricity_price_per_kwh'] : null,
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['count' => $e->getMessage()]);
        }

        return redirect()->route('admin.garages.index')->with(
            'success',
            "{$created->count()} garages created ({$created->first()->code} to {$created->last()->code})."
        );
    }
}