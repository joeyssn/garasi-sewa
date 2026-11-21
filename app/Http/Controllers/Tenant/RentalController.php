<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\RentalType;
use App\Enums\VehicleType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRentalRequest;
use App\Models\Garage;
use App\Services\RentalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RentalController extends Controller
{
    public function __construct(private RentalService $rentals)
    {
    }

    public function index(Request $request): View
    {
        $rentals = $request->user()->rentals()
            ->with(['garage', 'vehicle', 'extensions'])
            ->latest()
            ->paginate(10);

        return view('tenant.rentals.index', compact('rentals'));
    }

    public function create(): View
    {
        return view('tenant.rentals.create', [
            'vehicleTypes' => VehicleType::cases(),
            'today' => Carbon::today()->toDateString(),
        ]);
    }

    /** AJAX: garages of the chosen type that are free today. */
    public function availableGarages(Request $request): JsonResponse
    {
        $data = $request->validate([
            'vehicle_type' => ['required', Rule::enum(VehicleType::class)],
            'garage_type' => ['required', 'string'],
        ]);

        $allowed = array_map(fn ($t) => $t->value, VehicleType::from($data['vehicle_type'])->allowedGarageTypes());
        abort_unless(in_array($data['garage_type'], $allowed, true), 422, 'Garage type does not fit the vehicle.');

        $garages = Garage::where('type', $data['garage_type'])
            ->availableOn(Carbon::today())
            ->orderBy('code')
            ->get(['id', 'code', 'daily_price', 'electricity_price_per_kwh']);

        return response()->json($garages);
    }

    /** AJAX: end date and total cost preview. */
    public function quote(Request $request): JsonResponse
    {
        $data = $request->validate([
            'garage_id' => ['required', 'exists:garages,id'],
            'rental_type' => ['required', Rule::enum(RentalType::class)],
            'days' => ['nullable', 'required_if:rental_type,daily', 'integer', 'min:1', 'max:30'],
            'battery_capacity_kwh' => ['nullable', 'numeric', 'min:0'],
        ]);

        $type = RentalType::from($data['rental_type']);

        return response()->json($this->rentals->quote(
            Garage::findOrFail($data['garage_id']),
            $type,
            $type === RentalType::Daily ? (int) $data['days'] : null,
            isset($data['battery_capacity_kwh']) ? (float) $data['battery_capacity_kwh'] : null,
        ));
    }

    public function store(StoreRentalRequest $request): RedirectResponse
    {
        $rental = $this->rentals->createRental($request->user(), $request->validated(), $request->file('payment_proof'));

        return redirect()->route('rentals.index')
            ->with('success', "Garage {$rental->garage->code} rented successfully.");
    }
}