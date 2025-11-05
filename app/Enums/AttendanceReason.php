<?php

namespace App\Enums;

enum AttendanceReason : String
{
    case SICK     = 'Ziekte';
    case HOLIDAY     = 'Vakantie';
    case INJURY      = 'Blessure';
    case OTHER       = 'Anders';
}
