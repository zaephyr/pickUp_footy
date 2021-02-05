<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'event_date', 'completed'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'match_user', 'match_id', 'user_id')->withPivot(['id', 'squad'])
        ->as('participant');
    }

    public function squad($num)
    {
        return $this->users->where('participant.squad', $num);
    }
}
