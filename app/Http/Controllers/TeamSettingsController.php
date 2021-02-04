<?php

namespace App\Http\Controllers;

use App\Models\Match;
use App\Models\Membership;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeamSettingsController extends Controller
{
    public function createUser(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'max:16', 'min:8'],
            'team' => ['required'],
        ])->validate();

        $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

        Membership::create([
            'user_id' => $newUser->id,
            'team_id' => $request->team,
            'role' => $request->role,
        ]);

        $newUser->current_team_id = $request->team;
        $newUser->save();

        return back();
    }

    public function createMatch(Request $request)
    {
        Validator::make($request->all(), [
            'date' => ['required'],
            'team' => ['required'],
        ])->validate();

        $mydate = $request->date;

        // $parsed_date = Carbon::parse($mydate)->toDateTimeString();

        Match::create([
            'event_date' => $mydate,
            'team_id' => $request->team,
        ]);

        if ($request->attend) {
            $member = $request->user();
            $team = $request->user()->currentTeam;
            $member = $member->currentMembership($team);
            $member->attend = $request->attend;
            $member->update();
        }

        return redirect()->back()
                    ->with('message', 'New Match event added.');
    }
}
