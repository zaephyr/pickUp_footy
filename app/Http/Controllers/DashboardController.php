<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $team = $request->user()->currentTeam;
        $members = $team->users;

        return Inertia::render('Dashboard', [
            'currentTeamMember' => $request->user()->currentMembership,
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

    public function update(Request $request)
    {
        Validator::make($request->all(), [
            'attend' => ['required'],
        ])->validate();

        $member = $request->user()->currentMembership;
        $member->attend = $request->attend;
        $member->update();

        return redirect()->back()
                    ->with('message', 'Post Updated Successfully.');
    }
}
