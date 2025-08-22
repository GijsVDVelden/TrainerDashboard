<?php

namespace App\Enums;

enum EventType : string
{
    case Training = 'training';
    case Match    = 'match';
    case Other    = 'other';
}
