<?php
namespace App\Enums;

enum PlayerPositions: string
{
    case GK = 'Keeper';
    case LB = 'Linksback';
    case RB = 'Rechtsback';
    case CB = 'Centrale Verdediger';
    case CVM = 'Verdedigende Middenvelder';
    case CM = 'Centrale Middenvelder';
    Case CAM = 'Aanvallende Middenvelder';
    case LW = 'Linksbuiten';
    case RW = 'Rechtsbuiten';
    case CF = 'Spits';

}
