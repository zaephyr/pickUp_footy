<?php

namespace App\Http\Controllers;

use App\Models\Match;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MatchController extends Controller
{
    public function index(Match $match)
    {
        $squadGreen = array_values($match->squad(1)->all());
        $squadOrange = array_values($match->squad(0)->all());

        return Inertia::render('Match', [
            'matchData' => ['id' => $match->id, 'date' => $match->event_date],
            'squadGreen' => $squadGreen,
            'squadOrange' => $squadOrange,
        ]);
    }

    public function createSquads(Match $match, Request $request)
    {
        foreach ($request->managedTeams as $player) {
            $player = (object) $player;
            $user = User::find($player->id);

            if ($player->squad) {
                $match->users()->attach($user, ['squad' => 1]);
            } else {
                $match->users()->attach($user, ['squad' => 0]);
            }
        }

        return redirect()->back()->with('message', 'Squads Created Successfully.');
    }
}
