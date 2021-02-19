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
        $team = $request->user()->currentTeam;
        $userNow = $request->user()->currentMembership($team) ?? 0;

        $members = $team->users;

        $match = $team->matches;

        if ($match->count() == 0) {
            return Inertia::render('Dashboard', [
                'matchData' => ['id' => 0, 'date' => 'none'],
                'currentTeamMember' => $userNow,
                'members' => $members->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'member_id' => $user->membership->id,
                        'attend' => $user->membership->attend,
                    ];
                }),
            ]);
        }

        $match = $match->where('completed', 0)->first();
        $dt = $match->event_date;
        $match->event_date = Carbon::createFromFormat('Y-m-d', $dt)->diffForHumans();

        $match_users = $match->users ?? [];

        return Inertia::render('Dashboard', [
            'matchData' => [
                'match_id' => $match->id ?? 0,
                'date' => $match->event_date ?? 0,
                'managedSquads' => $match_users->map(function ($user) {
                    return [
                        'squad' => $user->participant->squad == 1 ? true : false,
                        'name' => $user->name,
                        'id' => $user->id,
                        'match_user_id' => $user->participant->id,
                    ];
                }),
            ],
            'currentTeamMember' => $userNow,
            'members' => $members->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
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

        $match_user = $request->user()->matches->where('completed', 0)->first();

        if ($request->attend == -1 && $match_user != null) {
            $match_user->pivot->delete();
        }

        $membership->attend = $request->attend;
        $membership->update();

        return redirect()->back()
                    ->with('message', 'Attend Updated Successfully.');
    }

    public function updateMass(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;

        if (!$user->hasTeamPermission($team, 'create')) {
            abort(401);
        }

        $memberArr = $request->membersToUpdate;
        foreach ($memberArr as $memberData) {
            $memberData = (object) $memberData;

            $member = Membership::find($memberData->id);

            $match_user = $member->user->matches->where('completed', 0)->first();
            if ($memberData->attend == -1 && $match_user != null) {
                $match_user->pivot->delete();
            }

            $member->attend = $memberData->attend;
            $member->update();
        }

        return redirect()->back()->with('message', 'Attendings Updated Successfully.');
    }
}
