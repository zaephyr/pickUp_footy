<?php

namespace App\Http\Controllers;

use App\Models\Match;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeamSettingsController extends Controller
{
    public function createUser(Team $team, Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'max:16', 'min:8'],
        ])->validate();

        $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

        Membership::create([
            'user_id' => $newUser->id,
            'team_id' => $team->id,
            'role' => $request->role,
        ]);

        $newUser->current_team_id = $team->id;
        $newUser->save();

        return back();
    }

    public function createMatch(Team $team, Request $request)
    {
        Validator::make($request->all(), [
            'date' => ['required'],
        ])->validate();

        $mydate = $request->date;

        Match::create([
            'event_date' => $mydate,
            'team_id' => $team->id,
        ]);

        if ($request->attend) {
            $member = $request->user()->currentMembership($team);
            $member->attend = $request->attend;
            $member->update();
        }

        return redirect()->back()
                    ->with('message', 'New Match event added.');
    }
}
