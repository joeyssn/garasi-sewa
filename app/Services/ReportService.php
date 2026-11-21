<?php

namespace App\Services;

use App\Enums\GarageType;
use App\Models\Rental;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/** Rental reports. Revenue = original total + all extension costs. */
class ReportService
{
    /** Per month in a given year (12 rows). */
    public function monthly(int $year): Collection
    {
        $rentals = $this->baseQuery()->whereYear('start_date', $year)->get();

        return collect(range(1, 12))->map(fn (int $month) => $this->row(
            Carbon::create(2000, $month, 1)->format('F'),
            $rentals->filter(fn (Rental $r) => $r->start_date->month === $month)
        ));
    }

    /** Per day in a given month. */
    public function daily(int $year, int $month): Collection
    {
        $rentals = $this->baseQuery()->whereYear('start_date', $year)->whereMonth('start_date', $month)->get();
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        return collect(range(1, $daysInMonth))->map(fn (int $day) => $this->row(
            sprintf('%04d-%02d-%02d', $year, $month, $day),
            $rentals->filter(fn (Rental $r) => $r->start_date->day === $day)
        ));
    }

    /** Per garage type. */
    public function byGarageType(): Collection
    {
        $rentals = $this->baseQuery()->get();

        return collect(GarageType::cases())->map(fn (GarageType $type) => $this->row(
            $type->label(),
            $rentals->filter(fn (Rental $r) => $r->garage->type === $type)
        ));
    }

    /** Per tenant. */
    public function byTenant(): Collection
    {
        return $this->baseQuery()->get()
            ->groupBy('user_id')
            ->map(fn (Collection $items) => $this->row(
                $items->first()->user->name . ' (' . $items->first()->user->email . ')',
                $items
            ))
            ->values();
    }

    private function baseQuery()
    {
        return Rental::with(['garage', 'user'])->withSum('extensions', 'additional_cost');
    }

    private function row(string $label, Collection $items): array
    {
        return [
            'label' => $label,
            'count' => $items->count(),
            'revenue' => (float) $items->sum(
                fn (Rental $r) => (float) $r->total_cost + (float) ($r->extensions_sum_additional_cost ?? 0)
            ),
        ];
    }
}