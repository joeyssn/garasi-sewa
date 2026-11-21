<?php
namespace App\Enums;

enum RentalType: string
{
    case Daily = 'daily';
    case Monthly = 'monthly';

    public function label(): string
    {
        return $this === self::Daily ? 'Daily' : 'Monthly';
    }
}