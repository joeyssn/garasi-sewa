<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** History of rental extensions made by the admin. */
class RentalExtension extends Model
{
    protected $fillable = [
        'rental_id', 'admin_id', 'old_end_date', 'new_end_date',
        'extra_days', 'additional_cost', 'payment_proof',
    ];

    protected function casts(): array
    {
        return [
            'old_end_date' => 'date',
            'new_end_date' => 'date',
            'additional_cost' => 'decimal:2',
        ];
    }

    public function rental(): BelongsTo { return $this->belongsTo(Rental::class); }
    public function admin(): BelongsTo { return $this->belongsTo(User::class, 'admin_id'); }
}