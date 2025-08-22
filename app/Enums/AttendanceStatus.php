<?php

namespace App\Enums;

enum AttendanceStatus : string
{
    case PRESENT     = 'Present';
    case ABSENT      = 'Absent';
    case LATE    = 'Late';
    case UNKNOWN    = 'Unknown';
}
