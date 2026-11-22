<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExtendRentalRequest;
use App\Models\Rental;
use App\Services\RentalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use InvalidArgumentException;

/** Admin: extend the end date of a garage rental. */
class RentalExtensionController extends Controller
{
    public function __construct(private RentalService $rentals)
    {
    }

    /** List of garages that are rented today. */
    public function index(): View
    {
        $active = Rental::with(['garage', 'user', 'vehicle'])
            ->activeOn(Carbon::today())
            ->orderBy('end_date')
            ->get();

        return view('admin.extensions.index', compact('active'));
    }

    /** Extension form for one rental. */
    public function edit(Rental $rental): View
    {
        $rental->load(['garage', 'user', 'vehicle', 'extensions']);

        return view('admin.extensions.edit', compact('rental'));
    }

    /** AJAX: remaining amount due for a new end date. */
    public function quote(Request $request, Rental $rental): JsonResponse
    {
        $request->validate(['new_end_date' => ['required', 'date']]);

        try {
            return response()->json($this->rentals->extensionQuote($rental, Carbon::parse($request->new_end_date)));
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /** Save the extension: new end date + proof of payment. */
    public function update(ExtendRentalRequest $request, Rental $rental): RedirectResponse
    {
        $this->rentals->extend(
            $rental,
            Carbon::parse($request->validated('new_end_date')),
            $request->file('payment_proof'),
            $request->user(),
        );

        return redirect()->route('admin.extensions.index')
            ->with('success', "Rental of garage {$rental->garage->code} extended.");
    }
}
