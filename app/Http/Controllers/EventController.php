<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function destroy(Event $event)
    {
        // Verwijder gerelateerde attendances
        $event->attendances()->delete();
        
        // Verwijder game stats als het een wedstrijd is
        if ($event->game) {
            $event->game->playerStats()->delete();
            $event->game->delete();
        }
        
        // Verwijder het event zelf
        $event->delete();
        
        return redirect()->back()->with('success', 'Evenement verwijderd');
    }
}
