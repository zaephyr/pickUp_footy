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

        return Inertia::render('Match/Match', [
            'matchData' => ['id' => $match->id, 'date' => $match->event_date],
            'squadGreen' => $squadGreen,
            'squadOrange' => $squadOrange,
        ]);
    }

    public function createSquads(Match $match, Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;

        if (!$user->hasTeamPermission($team, 'create')) {
            abort(401);
        }

        foreach ($request->managedSquads as $player) {
            $player = (object) $player;
            $user = User::find($player->id);

            $num = ($player->squad) ? 1 : 0;

            $updated = $match->users()->updateExistingPivot($user, ['squad' => $num]);
            if ($updated == 0) {
                $match->users()->attach($user, ['squad' => $num]);
            }
        }

        return redirect()->route('match', $match);
    }
}
