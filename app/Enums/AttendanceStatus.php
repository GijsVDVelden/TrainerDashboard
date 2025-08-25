<?php

namespace App\Enums;

enum AttendanceStatus : string
{
    case PRESENT     = 'Aanwezig';
    case ABSENT      = 'Afwezig';
    case LATE    = 'Laat';
    case SICK        = 'Ziek';
}
