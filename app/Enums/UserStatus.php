<?php // app/Enums/UserStatus.php
namespace App\Enums;

/** Registration status set by the admin. */
enum UserStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Waiting for verification',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }
}