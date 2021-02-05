<?php

namespace Database\Seeders;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $num = 11;
        // \App\Models\User::factory(10)->create();
        User::factory($num)->create();

        $users = User::all()->pluck('id')->toArray();
        shuffle($users);

        for ($i = 0; $i < $num; ++$i) {
            $user_id = $users[$i];
            Membership::create([
                'user_id' => $user_id,
                'team_id' => 1,
            ]);
        }
    }
}
