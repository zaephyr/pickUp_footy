<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userNow = $request->user();
        $team = $request->user()->currentTeam;
        $members = $team->users;
        // $currentMembership = $userNow->memberships->where('team_id', '=', $userNow->current_team_id)->first();

        $match = $team->matches;

        if ($match->count() == 0) {
            return Inertia::render('Dashboard', [
                'matchData' => ['id' => 0, 'date' => 'none'],
                'currentTeamMember' => 0,
                'members' => 0,
            ]);
        }

        $match = $match->where('completed', 0)->first();

        $dt = $match->event_date;

        $match->event_date = Carbon::createFromFormat('Y-m-d', $dt)->diffForHumans();

        return Inertia::render('Dashboard', [
            'matchData' => ['id' => $match->id ?? 0, 'date' => $match->event_date ?? 0],
            'currentTeamMember' => $userNow->currentMembership($team) ?? 0,
            'members' => $members->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'member_id' => $user->membership->id,
                    'attend' => $user->membership->attend,
                ];
            }),
        ]);
    }

    public function update(Membership $membership, Request $request)
    {
        Validator::make($request->all(), [
            'attend' => ['required'],
        ])->validate();

        $membership->attend = $request->attend;
        $membership->update();

        return redirect()->back()
                    ->with('message', 'Attend Updated Successfully.');
    }

    public function updateMass(Request $request)
    {
        $memberArr = $request->membersToUpdate;
        foreach ($memberArr as $memberData) {
            $memberData = (object) $memberData;

            $member = Membership::find($memberData->id);
            $member->attend = $memberData->attend;
            $member->update();
        }

        return redirect()->back()->with('message', 'Attendings Updated Successfully.');
    }
}
