<?php // app/Enums/GarageStatus.php
namespace App\Enums;

enum GarageStatus: string
{
    case Available = 'available';
    case Used = 'used';
}