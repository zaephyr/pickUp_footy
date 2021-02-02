<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\User;
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

        $newUser->save();
        Membership::create([
            'user_id' => $newUser->id,
            'team_id' => $request->team,
            'role' => $request->role,
        ]);

        return back();
    }
}
